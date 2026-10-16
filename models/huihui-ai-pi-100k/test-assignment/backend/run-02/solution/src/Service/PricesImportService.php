<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Exception\ImportException;
use App\Import\ModelDefinition;
use App\Import\PriceFile;
use App\Import\PriceRowDefinition;
use App\Import\ProviderDefinition;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка справочника цен:
 *  - провайдеры — upsert по code;
 *  - модели — upsert по (provider, code);
 *  - строки цен — upsert по (provider, model, valid_from).
 * Повторная загрузка тех же данных не изменяет содержимое БД.
 */
final class PricesImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
    ) {
    }

    /**
     * Применяет файл истории цен (одна транзакция).
     *
     * @return array{
     *     providers: int, models: int, prices: int,
     *     created_providers: int, created_models: int, created_prices: int
     * }
     *
     * @throws ImportException при неверных ссылках (неизвестный провайдер/модель)
     */
    public function apply(PriceFile $file): array
    {
        return $this->em->wrapInTransaction(function () use ($file): array {
            $stats = [
                'providers' => 0,
                'models' => 0,
                'prices' => 0,
                'created_providers' => 0,
                'created_models' => 0,
                'created_prices' => 0,
            ];

            $providerByCode = [];
            foreach ($file->providers as $definition) {
                $providerByCode[$definition->code] = $this->upsertProvider($definition, $stats);
            }

            $modelByKey = [];
            foreach ($file->models as $definition) {
                $key = $definition->providerCode . "\x1E" . $definition->code;
                $modelByKey[$key] = $this->upsertModel($definition, $providerByCode, $stats);
            }

            foreach ($file->prices as $definition) {
                $key = $definition->providerCode . "\x1E" . $definition->modelCode;

                $model = $modelByKey[$key]
                    ?? $this->models->findByProviderCodeAndCode($definition->providerCode, $definition->modelCode);
                if ($model === null) {
                    throw new ImportException(sprintf(
                        'Строка цены %s/%s (valid_from %s): модель не найдена в справочнике',
                        $definition->providerCode,
                        $definition->modelCode,
                        \gmdate('c', $definition->validFrom->getTimestamp()),
                    ));
                }

                $this->upsertPrice($model, $definition, $stats);
            }

            $this->em->flush();

            return $stats;
        });
    }

    /**
     * @param array{created_providers: int, providers: int} $stats
     */
    private function upsertProvider(ProviderDefinition $definition, array &$stats): Provider
    {
        $provider = $this->providers->findByCode($definition->code);
        if ($provider === null) {
            $provider = new Provider();
            $this->em->persist($provider);
            $stats['created_providers']++;
        }

        $provider->code = $definition->code;
        $provider->name = $definition->name;
        $provider->replaceOffPeak($definition->offPeak);

        $stats['providers']++;

        return $provider;
    }

    /**
     * @param array<string, Provider> $providerByCode
     * @param array{created_models: int, models: int} $stats
     *
     * @throws ImportException
     */
    private function upsertModel(ModelDefinition $definition, array $providerByCode, array &$stats): Model
    {
        $provider = $providerByCode[$definition->providerCode]
            ?? $this->providers->findByCode($definition->providerCode);
        if ($provider === null) {
            throw new ImportException(sprintf(
                'Модель %s/%s: провайдер "%s" неизвестен (нет ни в файле, ни в справочнике)',
                $definition->providerCode,
                $definition->code,
                $definition->providerCode,
            ));
        }

        $model = $this->models->findByProviderAndCode($provider, $definition->code);
        if ($model === null) {
            $model = new Model();
            $this->em->persist($model);
            $stats['created_models']++;
        }

        $model->provider = $provider;
        $model->code = $definition->code;

        $stats['models']++;

        return $model;
    }

    /**
     * @param array{created_prices: int, prices: int} $stats
     */
    private function upsertPrice(Model $model, PriceRowDefinition $definition, array &$stats): void
    {
        $price = $this->prices->findByModelAndValidFrom($model, $definition->validFrom);
        if ($price === null) {
            $price = new Price();
            $this->em->persist($price);
            $stats['created_prices']++;
        }

        $price->model = $model;
        $price->validFrom = $definition->validFrom;
        $price->currency = $definition->currency;
        $price->applyRates($definition->unit->value, $definition->rates);

        $stats['prices']++;
    }
}
