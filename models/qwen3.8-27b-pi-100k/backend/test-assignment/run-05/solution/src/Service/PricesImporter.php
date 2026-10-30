<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Shared\ImportException;
use App\Shared\InputValidator;
use Doctrine\ORM\EntityManagerInterface;

final class PricesImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(string $path): void
    {
        $data = self::readJsonFile($path, 'prices');
        $providers = InputValidator::requireStringArray($data, 'providers', 'prices');
        $models = InputValidator::requireStringArray($data, 'models', 'prices');
        $prices = InputValidator::requireStringArray($data, 'prices', 'prices');

        $this->em->wrapInTransaction(function () use ($providers, $models, $prices): void {
            $this->importProviders($providers);
            $this->importModels($models);
            $this->importPriceRows($prices);
        });
    }

    /** @param list<array<string, mixed>> $providers */
    private function importProviders(array $providers): void
    {
        foreach ($providers as $i => $entry) {
            $ctx = sprintf('prices: providers[%d]', $i);
            $code = InputValidator::requireString((array) $entry, 'code', $ctx);
            $name = InputValidator::requireString((array) $entry, 'name', $ctx);

            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
            if ($provider === null) {
                $provider = new Provider($code, $name);
                $this->em->persist($provider);
                $this->em->flush();
            }

            $provider->setName($name);

            $offPeak = $entry['off_peak'] ?? null;
            if ($offPeak === null) {
                $provider->setOffPeak(null, null, null);
            } else {
                if (!is_array($offPeak)) {
                    throw new ImportException(sprintf('%s: off_peak должно быть объектом или null', $ctx));
                }
                $windowStart = InputValidator::hhmm((string) ($offPeak['window_start'] ?? ''), $ctx.': off_peak');
                $windowEnd = InputValidator::hhmm((string) ($offPeak['window_end'] ?? ''), $ctx.': off_peak');
                $multiplier = InputValidator::decimal((string) ($offPeak['multiplier'] ?? ''), $ctx.': off_peak');
                $provider->setOffPeak($windowStart, $windowEnd, $multiplier);
            }
        }
    }

    /** @param list<array<string, mixed>> $models */
    private function importModels(array $models): void
    {
        foreach ($models as $i => $entry) {
            $ctx = sprintf('prices: models[%d]', $i);
            $providerCode = InputValidator::requireString((array) $entry, 'provider', $ctx);
            $code = InputValidator::requireString((array) $entry, 'code', $ctx);

            $provider = $this->findProviderOrThrow($providerCode, $ctx);
            $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $code]);
            if ($model === null) {
                $this->em->persist(new Model($provider, $code));
            }
        }
        $this->em->flush();
    }

    /** @param list<array<string, mixed>> $prices */
    private function importPriceRows(array $prices): void
    {
        foreach ($prices as $i => $entry) {
            $ctx = sprintf('prices: prices[%d]', $i);
            $providerCode = InputValidator::requireString((array) $entry, 'provider', $ctx);
            $modelCode = InputValidator::requireString((array) $entry, 'model', $ctx);
            $validFrom = InputValidator::utcDateTime(InputValidator::requireString((array) $entry, 'valid_from', $ctx), $ctx);
            $priceFields = new PriceFields();
            [$unit, $rateInput, $rateOutput, $rateCacheRead, $rateCacheWrite, $rateAny] =
                $priceFields->parse((array) $entry, $ctx);

            $model = $this->findModelOrThrow($providerCode, $modelCode, $ctx);

            $row = $this->em->getRepository(PriceRow::class)->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
            if ($row === null) {
                $row = new PriceRow($model, $validFrom);
            }
            $row->setCurrency(InputValidator::requireString((array) $entry, 'currency', $ctx));
            $row->applyUnitAndRates($unit, $rateInput, $rateOutput, $rateCacheRead, $rateCacheWrite, $rateAny);
            $this->em->persist($row);
        }
        $this->em->flush();
    }

    private function findProviderOrThrow(string $code, string $ctx): Provider
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($provider === null) {
            throw new ImportException(sprintf('%s: провайдер "%s" не найден/не определён', $ctx, $code));
        }

        return $provider;
    }

    private function findModelOrThrow(string $providerCode, string $modelCode, string $ctx): Model
    {
        $query = $this->em->createQuery(
            'SELECT m FROM App\Entity\Model m JOIN m.provider p WHERE p.code = :providerCode AND m.code = :modelCode'
        )
            ->setParameter('providerCode', $providerCode)
            ->setParameter('modelCode', $modelCode);

        $model = $query->getOneOrNullResult();
        if ($model === null) {
            throw new ImportException(sprintf('%s: модель "%s/%s" не найдена/не определена', $ctx, $providerCode, $modelCode));
        }

        return $model;
    }

    /** @return array<string, mixed> */
    private static function readJsonFile(string $path, string $kind): array
    {
        if (!is_readable($path)) {
            throw new ImportException(sprintf('%s: файл "%s" недоступен для чтения', $kind, $path));
        }
        try {
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('%s: некорректный JSON в "%s": %s', $kind, $path, $e->getMessage()));
        }
        if (!is_array($data)) {
            throw new ImportException(sprintf('%s: корень JSON должен быть объектом', $kind));
        }

        return $data;
    }
}
