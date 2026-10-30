<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\OffPeak;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка справочника:
 * провайдеры — по code; модели — по (provider, code); строки цен — по (provider, model, valid_from).
 * Повторная загрузка тех же данных не изменяет БД.
 */
final class PricesImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceFileParser $parser,
    ) {}

    /**
     * @param string $file файл истории цен
     *
     * @return array{providers: int, models: int, prices: int} число записей в секциях
     *
     * @throws ImportException при некорректном JSON или данных
     */
    public function import(string $file): array
    {
        $data = $this->parser->parseFile($file);

        $providers = $this->upsertProviders($data['providers']);
        $models = $this->upsertModels($data['models'], $providers);
        $prices = $this->upsertPrices($data['prices'], $providers);

        $this->em->flush();

        return ['providers' => count($data['providers']), 'models' => count($data['models']), 'prices' => count($data['prices'])];
    }

    /**
     * @param list<array<string, mixed>> $raws
     *
     * @return array<string, Provider> code => сущность
     */
    private function upsertProviders(array $raws): array
    {
        $repo = $this->em->getRepository(Provider::class);
        $entities = [];

        foreach ($raws as $i => $raw) {
            if (!\is_array($raw)) {
                throw new ImportException(sprintf('providers[%d]: запись должна быть объектом', $i));
            }

            $context = sprintf('providers[%d]', $i);
            $code = self::nonEmptyString($raw['code'] ?? null, 'code', $context);
            $name = \array_key_exists('name', $raw) && $raw['name'] !== null
                ? self::nonEmptyString($raw['name'], 'name', $context)
                : $code;

            /** @var Provider|null $provider */
            $provider = $repo->findOneBy(['code' => $code]) ?? new Provider();
            $provider->code = $code;
            $provider->name = $name;

            // off_peak: ключ в записи присутствует — значение задаём (в т.ч. null = снять скидку);
            // ключ отсутствует — текущее значение не трогаем (частичный upsert).
            if (\array_key_exists('off_peak', $raw)) {
                if ($raw['off_peak'] === null) {
                    $provider->offPeakStart = null;
                    $provider->offPeakEnd = null;
                    $provider->offPeakMultiplier = null;
                } else {
                    $window = $this->parser->parseOffPeak($raw, $context);
                    if ($window === null) {
                        throw new ImportException(sprintf('%s: off_peak задан, но не распознан', $context));
                    }

                    $provider->offPeakStart = $window->startText();
                    $provider->offPeakEnd = $window->endText();
                    $provider->offPeakMultiplier = $window->multiplier();
                }
            }

            $this->em->persist($provider);
            $this->em->flush();
            $entities[$code] = $provider;
        }

        return $entities;
    }

    /**
     * @param list<array<string, mixed>> $raws
     * @param array<string, Provider> $providerEntities
     */
    private function upsertModels(array $raws, array $providerEntities): void
    {
        foreach ($raws as $i => $raw) {
            if (!\is_array($raw)) {
                throw new ImportException(sprintf('models[%d]: запись должна быть объектом', $i));
            }

            $context = sprintf('models[%d]', $i);
            $providerCode = self::nonEmptyString($raw['provider'] ?? null, 'provider', $context);
            $code = self::nonEmptyString($raw['code'] ?? null, 'code', $context);

            /** @var Model|null $model */
            $model = $this->em->getRepository(Model::class)->findOneBy([
                'provider' => $providerEntities[$providerCode] ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]),
                'code' => $code,
            ]);
            if ($model === null) {
                /** @var Provider|null $provider */
                $provider = $providerEntities[$providerCode]
                    ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
                if ($provider === null) {
                    throw new ImportException(sprintf(
                        '%s: провайдер "%s" не найден (нет в секции providers файла и не загружен в базу)',
                        $context,
                        $providerCode,
                    ));
                }

                $model = new Model();
                $model->provider = $provider;
                $model->code = $code;
                $this->em->persist($model);
            }

            $this->em->flush();
        }
    }

    /**
     * @param list<array<string, mixed>> $raws
     * @param array<string, Provider> $providerEntities
     */
    private function upsertPrices(array $raws, array $providerEntities): void
    {
        foreach ($raws as $i => $raw) {
            if (!\is_array($raw)) {
                throw new ImportException(sprintf('prices[%d]: запись должна быть объектом', $i));
            }

            $context = sprintf('prices[%d]', $i);
            $spec = $this->parser->parsePrice($raw, $context);

            /** @var Provider|null $provider */
            $provider = $providerEntities[$spec['provider']]
                ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $spec['provider']]);
            if ($provider === null) {
                throw new ImportException(sprintf(
                    '%s: провайдер "%s" не найден (нет в секции providers файла и не загружен в базу)',
                    $context,
                    $spec['provider'],
                ));
            }

            /** @var Model|null $model */
            $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $spec['model']]);
            if ($model === null) {
                throw new ImportException(sprintf(
                    '%s: модель "%s" провайдера "%s" не найдена (нет в секции models файла и не загружена в базу)',
                    $context,
                    $spec['model'],
                    $spec['provider'],
                ));
            }

            /** @var PriceRow|null $row */
            $row = $this->em->getRepository(PriceRow::class)->findOneBy(['model' => $model, 'validFrom' => $spec['valid_from']]);
            if ($row === null) {
                $row = new PriceRow();
                $row->model = $model;
                $this->em->persist($row);
            }

            $row->validFrom = $spec['valid_from'];
            $row->currency = $spec['currency'];
            $row->unit = $spec['spec']->unit;
            $row->rates = $spec['spec']->rates;

            $this->em->flush();
        }
    }

    /** @param mixed $value */
    private static function nonEmptyString($value, string $field, string $context): string
    {
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: поле "%s" обязательно и должно быть непустой строкой', $context, $field));
        }

        return $value;
    }
}
