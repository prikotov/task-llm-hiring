<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Exception\DataImportException;
use App\Infrastructure\Persistence\PriceRowRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника: провайдеры — по code,
 * модели — по (provider, code), строки цен — по (provider, model, valid_from).
 * Повторная загрузка тех же сумм не изменяет данных (нет dirty-сущностей).
 */
final class CatalogImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceRowRepository $priceRows,
    ) {
    }

    public function import(string $file): void
    {
        $data = CatalogInput::readJson($file);

        $providers = CatalogInput::parseProviders($data, $file);
        $models = CatalogInput::parseModels($data, $file);
        $prices = CatalogInput::parsePrices($data, $file);

        $this->em->wrapInTransaction(function () use ($providers, $models, $prices): void {
            // code -> сущность (файл + ранее загруженные из БД ниже)
            $providerRefs = [];
            foreach ($providers as [$code, $name, $opStart, $opEnd, $opMultiplier]) {
                $provider = $this->em->find(Provider::class, $code);
                $provider ??= new Provider($code, $name);
                $provider->apply($name, $opStart, $opEnd, $opMultiplier);
                $this->em->persist($provider);
                $providerRefs[$code] = $provider;
            }

            // (provider, code) -> сущность модели из файла
            $modelRefs = [];
            foreach ($models as [$providerCode, $modelCode]) {
                $providerRefs[$providerCode] ??= $this->resolveProvider($providerCode);
                $model = $this->em->getRepository(Model::class)->findOneBy([
                    'providerCode' => $providerCode,
                    'code' => $modelCode,
                ]);
                $model ??= new Model($providerCode, $modelCode);
                $this->em->persist($model);
                $modelRefs[$providerCode.'|'.$modelCode] = $model;
            }

            foreach ($prices as $price) {
                $key = $price['provider'].'|'.$price['model'];
                $model = $modelRefs[$key] ?? $this->resolveModel($price['provider'], $price['model']);
                $this->em->persist($model);

                $row = $this->priceRows->findOneByModelAndValidFrom(
                    $model->providerCode(),
                    $model->code(),
                    $price['validFrom'],
                );
                if ($row === null) {
                    $row = new PriceRow(
                        $model,
                        $price['validFrom'],
                        $price['currency'],
                        $price['unit'],
                        $price['rates'],
                    );
                } else {
                    $row->apply($price['currency'], $price['unit'], $price['rates']);
                }
                $this->em->persist($row);
            }
        });
    }

    private function resolveProvider(string $code): Provider
    {
        $provider = $this->em->find(Provider::class, $code);
        if ($provider === null) {
            throw new DataImportException(sprintf('Provider "%s" is not found in the reference data.', $code));
        }

        return $provider;
    }

    private function resolveModel(string $providerCode, string $code): Model
    {
        $model = $this->em->getRepository(Model::class)->findOneBy([
            'providerCode' => $providerCode,
            'code' => $code,
        ]);
        if ($model === null) {
            throw new DataImportException(
                sprintf('Model "%s/%s" is not found in the reference data.', $providerCode, $code),
            );
        }

        return $model;
    }
}
