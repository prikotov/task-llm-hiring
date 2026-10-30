<?php

declare(strict_types=1);

namespace App\Application\Ingestion;

use App\Domain\Billing\Cost;
use App\Domain\DomainException;
use App\Domain\Pricing\BillingUnit;
use App\Domain\Pricing\Model;
use App\Domain\Pricing\Provider;
use App\Domain\Pricing\StoredPriceRow;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный импорт справочника цен (JSON) в БД.
 *
 * Проставляет:
 *  - провайдеры — по `code`;
 *  - модели — по `(provider, code)`;
 *  - строки цен — по `(provider, model, valid_from)`.
 *
 * Повторная загрузка тех же сумм — не изменяет данных (upsert).
 */
final class PricesImportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @throws DomainException */
    public function import(string $jsonFile): void
    {
        $content = file_get_contents($jsonFile);
        if ($content === false) {
            throw DomainException::wrap(new \RuntimeException("cannot read file: {$jsonFile}"), 'PricesImportService::import');
        }

        $input = (new PriceCatalogParser())->parse($content);

        $em = $this->em;
        try {
            $em->wrapInTransaction(function () use ($input, $em): void {
                $this->upsertProviders($input, $em);
                $this->upsertModels($input, $em);
                $this->upsertPriceRows($input, $em);
            });
        } catch (\Throwable $e) {
            throw DomainException::wrap($e, 'PricesImportService::import');
        }
    }

    private function upsertProviders(PriceCatalogInput $input, EntityManagerInterface $em): void
    {
        foreach ($input->providers as $code => $info) {
            $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $code]);
            if ($provider === null) {
                $provider = new Provider();
                $provider->code = $code;
                $em->persist($provider);
            }
            Provider::applyTo($provider, $code, $info['off_peak']);
            $provider->name = $info['name'];
        }
    }

    private function upsertModels(PriceCatalogInput $input, EntityManagerInterface $em): void
    {
        foreach ($input->models as $m) {
            $row = $em->getRepository(Model::class)->findOneBy(
                ['provider' => $m['provider'], 'code' => $m['code']]
            );
            if ($row === null) {
                $model = new Model();
                $model->provider = $m['provider'];
                $model->code = $m['code'];
                $em->persist($model);
            }
        }
    }

    private function upsertPriceRows(PriceCatalogInput $input, EntityManagerInterface $em): void
    {
        /** @var StoredPriceRow[] $rows */
        $rows = $input->priceRows;
        /** @var StoredPriceRow $row */
        foreach ($rows as $row) {
            $existing = $em->getRepository(StoredPriceRow::class)->findOneBy([
                'provider' => $row->provider,
                'model' => $row->model,
                'validFrom' => $row->validFrom,
            ]);
            $domain = $row->toDomain();
            if ($existing === null) {
                $em->persist($row);
            } else {
                StoredPriceRow::applyTo(
                    $existing,
                    provider: $row->provider,
                    model: $row->model,
                    validFrom: $row->validFromDateTime(),
                    currency: $row->currency,
                    unit: $domain->unit,
                    rates: $domain->rates,
                );
            }
        }
    }
}
