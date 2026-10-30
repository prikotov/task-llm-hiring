<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\EntityTally;
use App\Application\Dto\PricesImportSummary;
use App\Billing\Pricing\PriceUnit;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Shared\Exception\BadInputException;
use App\Support\DecimalLiteral;
use App\Support\JsonFile;
use App\Support\SpecReader;
use App\Support\StrictIsoUtc;
use App\Support\TimeOfDay;
use Doctrine\ORM\EntityManagerInterface;

/**
 * app:prices:import — idempotentный upsert справочника:
 * провайдеры — по code, модели — по (provider, code),
 * строки цен — по (provider, model, valid_from).
 *
 * Повторная загрузка тех же данных не меняет таблиц (no-op),
 * уже тарифицированные события импортом цен не затрагивается.
 */
final class PricesImportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @throws BadInputException */
    public function import(string $path): PricesImportSummary
    {
        $data = JsonFile::readObject($path);
        // prices — обязательная непустая секция; providers и models — опциональны
        // (режим коррекции: строки цен к уже существующим справочникам).
        $providers = JsonFile::listSection($data, 'providers', false);
        $models = JsonFile::listSection($data, 'models', false);
        $prices = JsonFile::listSection($data, 'prices');
        if ($prices === []) {
            throw new BadInputException('Секция «prices» должна содержать хотя бы одну строку цен');
        }

        $providersTally = new EntityTally();
        $modelsTally = new EntityTally();
        $priceTally = new EntityTally();

        $this->em->getConnection()->transactional(function () use (
            $providers,
            $models,
            $prices,
            $providersTally,
            $modelsTally,
            $priceTally,
        ): void {
            $providersMap = $this->upsertProviders($providers, $providersTally);
            $modelsMap = $this->upsertModels($models, $providersMap, $modelsTally);
            $this->upsertPriceRows($prices, $providersMap, $modelsMap, $priceTally);

            // DBAL-транзакция сама по себе ORM-сущности не флешит.
            $this->em->flush();
        });

        return new PricesImportSummary($providersTally, $modelsTally, $priceTally);
    }

    /** @return array<string, Provider> code → сущность */
    private function upsertProviders(array $entries, EntityTally $tally): array
    {
        $map = [];
        foreach ($entries as $index => $raw) {
            $context = "providers[{$index}]";
            $entry = JsonFile::item($entries, $index, 'providers');

            $code = SpecReader::requiredString($entry, 'code', $context, 128);
            $name = SpecReader::requiredString($entry, 'name', $context, 255);
            [$winStart, $winEnd, $multiplier] = $this->readOffPeak($entry, $context);

            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]) ?? new Provider($code, $name);
            $isNew = $provider->getId() === null;

            if ($provider->getName() !== $name) {
                $provider->setName($name);
            }
            $this->applyOffPeak($provider, $winStart, $winEnd, $multiplier);

            if ($isNew) {
                $this->em->persist($provider);
                ++$tally->created;
            } else {
                ++$tally->updated;
            }

            $map[$code] = $provider;
        }

        return $map;
    }

    /**
     * @return array{string|null, string|null, string|null} окно оф-пика (null — скидки нет)
     */
    private function readOffPeak(array $entry, string $context): array
    {
        $offPeak = $entry['off_peak'] ?? null;
        if ($offPeak === null) {
            return [null, null, null];
        }

        if (!is_array($offPeak)) {
            throw new BadInputException("{$context}.off_peak: ожидается объект или null");
        }

        $context .= '.off_peak';
        $start = SpecReader::requiredString($offPeak, 'window_start', $context, 5);
        $end = SpecReader::requiredString($offPeak, 'window_end', $context, 5);
        TimeOfDay::parseHhMm($start);
        TimeOfDay::parseHhMm($end);

        $multiplier = SpecReader::requiredString($offPeak, 'multiplier', $context, 20);
        DecimalLiteral::assertNonNegative($multiplier, "{$context}.multiplier");
        if (bccomp($multiplier, '0', 12) <= 0) {
            throw new BadInputException("{$context}.multiplier: должно быть строго положительным");
        }

        return [$start, $end, $multiplier];
    }

    /**
     * Записывает окно оф-пика целиком; null-кортеж — «скидки нет»,
     * существующее окно счищается (повторная загрузка idempotentна).
     */
    private function applyOffPeak(Provider $provider, ?string $start, ?string $end, ?string $multiplier): void
    {
        $provider->setOffPeak($start, $end, $multiplier);
    }

    /**
     * @param array<string, Provider> $providersMap
     * @return array<string, Model> "provider/code" → сущность
     */
    private function upsertModels(array $entries, array $providersMap, EntityTally $tally): array
    {
        $map = [];
        foreach ($entries as $index => $raw) {
            $context = "models[{$index}]";
            $entry = JsonFile::item($entries, $index, 'models');

            $providerCode = SpecReader::requiredString($entry, 'provider', $context, 128);
            $code = SpecReader::requiredString($entry, 'code', $context, 128);

            $provider = $providersMap[$providerCode]
                ?? throw new BadInputException("{$context}.provider: провайдер «{$providerCode}» не найден в справочнике");

            $model = $this->em
                ->getRepository(Model::class)
                ->findOneBy(['provider' => $provider, 'code' => $code]);

            if ($model !== null) {
                // У модели нет изменяемых атрибутов: существующая запись — idempotentный no-op.
                $map[$providerCode.'/'.$code] = $model;
                continue;
            }

            $model = new Model($provider, $code);
            $this->em->persist($model);
            ++$tally->created;
            $map[$providerCode.'/'.$code] = $model;
        }

        return $map;
    }

    /**
     * @param array<string, Provider> $providersMap
     * @param array<string, Model> $modelsMap
     */
    private function upsertPriceRows(array $entries, array $providersMap, array $modelsMap, EntityTally $tally): void
    {
        foreach ($entries as $index => $raw) {
            $context = "prices[{$index}]";
            $entry = JsonFile::item($entries, $index, 'prices');

            $providerCode = SpecReader::requiredString($entry, 'provider', $context, 128);
            $modelCode = SpecReader::requiredString($entry, 'model', $context, 128);
            $label = $providerCode.'/'.$modelCode;

            // Модель берём из файла, а если её там нет — из справочника БД.
            $model = $modelsMap[$label] ?? null;
            if ($model === null) {
                $provider = $providersMap[$providerCode]
                    ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
                if ($provider !== null) {
                    $model = $this->em->getRepository(Model::class)
                        ->findOneBy(['provider' => $provider, 'code' => $modelCode]);
                }
            }
            if ($model === null) {
                throw new BadInputException("{$context}: модель «{$label}» не найдена в справочнике (декларирована в файле или в базе)");
            }

            $validFrom = StrictIsoUtc::parse(SpecReader::requiredString($entry, 'valid_from', $context, 40));
            $currency = SpecReader::requiredString($entry, 'currency', $context, 16);
            $unit = PriceUnit::fromWire(SpecReader::requiredString($entry, 'unit', $context, 40));
            $rates = SpecReader::rates($entry, $unit, $context);

            $row = $this->em
                ->getRepository(PriceRow::class)
                ->findOneBy(['model' => $model, 'validFrom' => $validFrom]);

            if ($row === null) {
                $this->em->persist(new PriceRow($model, $validFrom, $currency, $unit->value, $rates));
                ++$tally->created;
            } elseif ($row->getUnit() !== $unit->value
                || $row->getCurrency() !== $currency
                || $row->getRates() !== $rates
            ) {
                $row->setTerms($currency, $unit->value, $rates);
                ++$tally->updated;
            }
        }
    }
}
