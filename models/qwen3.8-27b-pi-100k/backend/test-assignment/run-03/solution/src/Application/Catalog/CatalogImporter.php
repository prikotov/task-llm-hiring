<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Shared\InputFormatException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка истории цен: идемпотентный upsert
 *   провайдеры — по code;
 *   модели     — по (provider, code);
 *   строки цен — по (provider, model, valid_from).
 *
 * Ссылки разрешаются «в файле или уже загруженном справочнике».
 * Повторная загрузка тех же данных данные не изменяет.
 */
final class CatalogImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(string $path): CatalogImportSummary
    {
        $file = (new CatalogFileLoader(false))->load($path);

        return $this->em->wrapInTransaction(function () use ($file): CatalogImportSummary {
            /** @var array<string, Provider> $providersByCode */
            $providersByCode = [];
            $newProviders = 0;

            foreach ($file->providers as $spec) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $spec->code]);
                if ($provider === null) {
                    $provider = new Provider($spec->code, $spec->name);
                    $this->em->persist($provider);
                    $this->em->flush();
                    ++$newProviders;
                } else {
                    $provider->setName($spec->name);
                    $provider->setOffPeak($spec->offPeak);
                }
                $providersByCode[$spec->code] = $provider;
            }

            /** @var array<string, Model> $modelsByKey "provider/code" */
            $modelsByKey = [];
            $newModels = 0;

            foreach ($file->models as $spec) {
                $provider = $this->resolveProvider($spec->provider, $providersByCode, "model '{$spec->code}'");
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $spec->code]);
                if ($model === null) {
                    $model = new Model($provider, $spec->code);
                    $this->em->persist($model);
                    $this->em->flush();
                    ++$newModels;
                }
                $modelsByKey[$spec->provider.'/'.$spec->code] = $model;
            }

            $newPriceRows = 0;

            foreach ($file->prices as $spec) {
                $model = $this->resolveModel($spec->provider, $spec->model, $modelsByKey);
                $key = $spec->provider.'/'.$spec->model;

                $row = $this->em->getRepository(PriceRow::class)->findOneBy([
                    'model' => $model,
                    'validFrom' => $spec->validFrom,
                ]);
                if ($row === null) {
                    $this->em->persist(new PriceRow($model, $spec->validFrom, $spec->currency, $spec->unit, $spec->rates));
                    ++$newPriceRows;
                } else {
                    $row->replaceTerms($spec->currency, $spec->unit, $spec->rates);
                }
            }

            $this->em->flush();

            return new CatalogImportSummary(
                count($file->providers),
                $newProviders,
                count($file->models),
                $newModels,
                count($file->prices),
                $newPriceRows,
            );
        });
    }

    /**
     * @param array<string, Provider> $providersByCode
     */
    private function resolveProvider(string $code, array $providersByCode, string $what): Provider
    {
        if (isset($providersByCode[$code])) {
            return $providersByCode[$code];
        }

        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($provider === null) {
            throw new InputFormatException($what.": провайдер \"{$code}\" отсутствует — его нужно объявить в секции providers файла или загрузить раньше");
        }

        return $provider;
    }

    /**
     * @param array<string, Model> $modelsByKey
     */
    private function resolveModel(string $providerCode, string $modelCode, array $modelsByKey): Model
    {
        $key = $providerCode.'/'.$modelCode;
        if (isset($modelsByKey[$key])) {
            return $modelsByKey[$key];
        }

        $provider = $this->resolveProvider($providerCode, [], $key);
        $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
        if ($model === null) {
            throw new InputFormatException("строка цены {$key}: модель отсутствует — объявите её в секции models файла или загрузите раньше");
        }

        return $model;
    }
}
