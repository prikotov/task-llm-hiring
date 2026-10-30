<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Domain\Pricing\Model;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка провайдеров, моделей и строк цен (upsert):
 *  - провайдеры — по code;
 *  - модели — по (provider, code);
 *  - строки цен — по (provider, model, valid_from).
 *
 * Повторная загрузка тех же данных не изменяет базу; импорт выполняется
 * в одной транзакции — либо весь файл взят, либо ни одной записи.
 */
final class PricesImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{providers:int, models:int, price_rows:int} число записей каждого раздела в файле
     */
    public function import(PriceFileSpec $spec): array
    {
        $em = $this->em;

        return $em->getConnection()->transactional(static function () use ($em, $spec): array {
            $providers = self::upsertProviders($em, $spec->providers);
            $models = self::upsertModels($em, $spec->models, $providers);
            self::upsertPriceRows($em, $spec->priceRows, $providers, $models);

            $em->flush();

            return [
                'providers' => count($spec->providers),
                'models' => count($spec->models),
                'price_rows' => count($spec->priceRows),
            ];
        });
    }

    /**
     * @param ProviderSpec[] $specs
     *
     * @return array<string, Provider> code => сущность
     */
    private static function upsertProviders(EntityManagerInterface $em, array $specs): array
    {
        $result = [];
        foreach ($specs as $spec) {
            $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $spec->code]);
            if ($provider === null) {
                $provider = new Provider(
                    $spec->code,
                    $spec->name,
                    $spec->offPeak?->startMinutes,
                    $spec->offPeak?->endMinutes,
                    $spec->offPeak?->multiplier,
                );
                $em->persist($provider);
            } else {
                $provider->update(
                    $spec->name,
                    $spec->offPeak?->startMinutes,
                    $spec->offPeak?->endMinutes,
                    $spec->offPeak?->multiplier,
                );
            }
            $result[$spec->code] = $provider;
        }

        return $result;
    }

    /**
     * @param ModelSpec[] $specs
     * @param array<string, Provider> $providers
     *
     * @return array<string, Model> "provider\x1fcode" => сущность
     */
    private static function upsertModels(EntityManagerInterface $em, array $specs, array $providers): array
    {
        $result = [];
        $resolver = self::providerResolver($em, $providers);

        foreach ($specs as $spec) {
            $provider = $resolver($spec->provider);
            $key = $spec->provider."\x1f".$spec->code;

            $model = $em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $spec->code]);
            if ($model === null) {
                $model = new Model($spec->code, $provider);
                $em->persist($model);
            }
            $result[$key] = $model;
        }

        return $result;
    }

    /**
     * @param PriceRowSpec[] $specs
     * @param array<string, Provider> $providers
     * @param array<string, Model> $models
     */
    private static function upsertPriceRows(EntityManagerInterface $em, array $specs, array $providers, array $models): void
    {
        $resolver = self::providerResolver($em, $providers);

        foreach ($specs as $spec) {
            $provider = $resolver($spec->provider);
            $key = $spec->provider."\x1f".$spec->model;

            $model = isset($models[$key])
                ? $models[$key]
                : $em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $spec->model]);

            if ($model === null) {
                throw new \App\Exception\InvalidPricesFileException(sprintf('price row: unknown model "%s/%s"', $spec->provider, $spec->model));
            }

            $row = $em->getRepository(PriceRow::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $spec->validFrom,
            ]);

            if ($row === null) {
                $row = new PriceRow(
                    $provider,
                    $model,
                    $spec->validFrom,
                    $spec->currency,
                    $spec->unit,
                    $spec->rates['input'] ?? null,
                    $spec->rates['output'] ?? null,
                    $spec->rates['cache_read'] ?? null,
                    $spec->rates['cache_write'] ?? null,
                    $spec->rates['any'] ?? null,
                );
                $em->persist($row);
            } else {
                $row->replaceRates($spec->currency, $spec->unit, $spec->rates);
            }
        }
    }

    /**
     * @param array<string, Provider> $providers
     */
    private static function providerResolver(EntityManagerInterface $em, array $providers): callable
    {
        return static function (string $code) use ($em, $providers): Provider {
            if (isset($providers[$code])) {
                return $providers[$code];
            }

            $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $code]);
            if ($provider === null) {
                throw new \App\Exception\InvalidPricesFileException(sprintf('unknown provider "%s"', $code));
            }

            return $provider;
        };
    }
}
