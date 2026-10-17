<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\DomainException;
use App\Ingest\PriceFile;
use App\Ingest\PriceEntry;
use App\Ingest\ProviderData;
use App\Entity\PriceRow;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника.
 *
 * Провайдеры — по code; модели — по (provider, code);
 * строки цен — по (provider, model, valid_from), суммы заменяются.
 */
final class PriceImportService
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    /**
     * @return array<int, int> id строк цен в порядке файла
     */
    public function import(EntityManagerInterface $em, PriceFile $file): array
    {
        // Весь файл — в одной транзакции (атомарность upsert-а справочника).
        $em->beginTransaction();
        try {
            $providerIds = [];
            foreach ($file->providers as $data) {
                $p = $this->catalog->upsertProvider($em, $data->code, $data->name, $data->offPeak, $data->offPeakSpecified);
                $providerIds[$data->code] = $p->id;
            }

            $priceRowIds = [];
            foreach ($file->prices as $entry) {
                $providerId = $this->requiredProviderId($em, $providerIds, $entry->providerCode);
                $modelId = $this->catalog->upsertModel($em, $providerId, $entry->modelCode);
                $priceRowIds[] = $this->upsertPriceRow($em, $modelId, $entry);
            }

            $em->flush();
            $em->commit();
        } catch (\Throwable $e) {
            $em->rollback();
            $em->clear();

            throw $e;
        }

        return $priceRowIds;
    }

    private function requiredProviderId(EntityManagerInterface $em, array $providerIds, string $code): int
    {
        if (isset($providerIds[$code])) {
            return $providerIds[$code];
        }
        $provider = $this->catalog->providerByCode($em, $code);
        if ($provider !== null) {
            return $provider->id;
        }

        throw new DomainException(sprintf('Неизвестный провайдер "%s"', $code));
    }

    /**
     * @return int id строки цены
     */
    private function upsertPriceRow(EntityManagerInterface $em, int $modelId, PriceEntry $entry): int
    {
        /** @var PriceRow|null $row */
        $row = $em->createQuery(
            'SELECT pr FROM '.PriceRow::class.' pr JOIN pr.model m WHERE m.id = :mid AND pr.validFrom = :vf'
        )
            ->setParameter('mid', $modelId)
            ->setParameter('vf', $entry->validFrom)
            ->getOneOrNullResult();

        if ($row === null) {
            $model = $em->find(\App\Entity\Model::class, $modelId);
            assert($model instanceof \App\Entity\Model);
            $row = new PriceRow();
            $row->model = $model;
            $em->persist($row);
        }

        $row->validFrom = $entry->validFrom;
        $row->currency = $entry->currency;
        $row->unit = $entry->unit;
        $row->rates = $entry->rates;
        $em->flush(); // получить id на случай новой строки цены

        return $row->id;
    }
}
