<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Result\ImportSummary;
use App\Domain\Exception\BillingException;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Repository\ModelRepository;
use App\Repository\ProviderRepository;
use App\Repository\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт (идемпотентный upsert) справочника провайдеров, моделей
 * и строк цен из файла формата задания.
 *
 * Ключи upsert:
 *  - провайдер — code;
 *  - модель — (provider, code);
 *  - строка цен — (provider, model, valid_from).
 *
 * Повторная загрузка тех же сумм данных не изменяет;
 * изменение значений (например, new off_peak) — заменяет.
 * Уже рассчитанные события при обычном импорте не трогаются —
 * их коррекция — отдельная команда app:billing:reprice.
 */
final class PriceCatalogImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
        private readonly PricesFileParser $parser,
    ) {
    }

    public function import(string $file): ImportSummary
    {
        $data = $this->parser->parseFile($file);
        $providersCount = 0;
        $modelsCount = 0;
        $pricesCount = 0;

        $this->em->wrapInTransaction(function () use ($data, &$providersCount, &$modelsCount, &$pricesCount): void {
            $modelMap = []; // "provider\0code" => Model

            foreach ($data->providers as $providerData) {
                $provider = $this->providers->findByCode($providerData->code)
                    ?? new Provider($providerData->code, $providerData->name);

                $provider->setName($providerData->name);
                $provider->setOffPeak($providerData->windowStart, $providerData->windowEnd, $providerData->multiplier);
                $this->em->persist($provider);
                $providersCount++;
            }

            // Фиксируем провайдеров до поиска моделей, ссылающихся на них,
            // чтобы выборки видели свежие записи.
            $this->em->flush();

            foreach ($data->models as $modelData) {
                $key = $modelData->provider."\0".$modelData->code;
                $model = $modelMap[$key] ?? $this->models->findByProviderCodeAndCode($modelData->provider, $modelData->code);
                if ($model === null) {
                    $provider = $this->providers->findByCode($modelData->provider)
                        ?? throw new BillingException(sprintf(
                            'Не найден провайдер "%s", на которого указывает модель "%s"',
                            $modelData->provider,
                            $modelData->code,
                        ));
                    $model = new Model($provider, $modelData->code);
                    $this->em->persist($model);
                }
                $modelMap[$key] = $model;
                $modelsCount++;
            }

            // Фиксируем модели до поиска/создания строк цен.
            $this->em->flush();

            foreach ($data->prices as $priceData) {
                $key = $priceData->provider."\0".$priceData->model;
                $model = $modelMap[$key] ?? $this->models->findByProviderCodeAndCode($priceData->provider, $priceData->model);
                if ($model === null) {
                    throw new BillingException(sprintf(
                        'Не найдена модель "%s/%s" (провайдер и/или модель не импортированы)',
                        $priceData->provider,
                        $priceData->model,
                    ));
                }

                $price = $this->prices->findByModelAndValidFrom($model, $priceData->validFrom);
                if ($price === null) {
                    $price = new Price($model, $priceData->validFrom, $priceData->currency, $priceData->unit, $priceData->rates);
                    $this->em->persist($price);
                } else {
                    $price->update($priceData->currency, $priceData->unit, $priceData->rates);
                }
                $pricesCount++;
            }
        });

        return new ImportSummary($providersCount, $modelsCount, $pricesCount);
    }
}
