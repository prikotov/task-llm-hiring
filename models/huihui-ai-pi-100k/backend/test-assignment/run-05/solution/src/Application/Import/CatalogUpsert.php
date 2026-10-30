<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Infrastructure\Persistence\Entity\Model;
use App\Infrastructure\Persistence\Entity\PriceRow;
use App\Infrastructure\Persistence\Entity\Provider;
use App\Infrastructure\Persistence\PriceRowMapper;
use App\Infrastructure\Persistence\Repository\PriceRowRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентные upserts справочника:
 *  - провайдеры — по code;
 *  - модели — по (provider, code);
 *  - строки цен — по (provider, model, valid_from).
 *
 * Вставка идёт через ORM-методы (persist), дедупликация —
 * по природным ключам в репозитории.
 */
final class CatalogUpsert
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function upsertProvider(ProviderRecord $record): void
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $record->code]);

        if ($provider === null) {
            $provider = new Provider();
            $provider->code = $record->code;
            $this->em->persist($provider);
        }

        $provider->name = $record->name;
        $provider->offPeakStart = $record->offPeakStart;
        $provider->offPeakEnd = $record->offPeakEnd;
        $provider->offPeakMultiplier = $record->offPeakMultiplier;
    }

    public function upsertModel(ModelRecord $record): void
    {
        $model = $this->em->getRepository(Model::class)->findOneBy([
            'provider' => $record->provider,
            'code' => $record->code,
        ]);

        if ($model === null) {
            $model = new Model();
            $model->provider = $record->provider;
            $model->code = $record->code;
            $this->em->persist($model);
        }
    }

    public function upsertPriceRow(PriceRecord $record): void
    {
        /** @var PriceRowRepository $repository */
        $repository = $this->em->getRepository(PriceRow::class);

        $priceRow = $repository->findByNaturalKey($record->provider, $record->model, $record->validFrom);

        if ($priceRow === null) {
            $priceRow = new PriceRow();
            $priceRow->provider = $record->provider;
            $priceRow->model = $record->model;
            $priceRow->validFrom = $record->validFrom;
            $this->em->persist($priceRow);
        }

        PriceRowMapper::apply($priceRow, $record->spec, $record->currency);
    }
}
