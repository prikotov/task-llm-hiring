<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\Model;
use App\Entity\PriceRow;
use App\Repository\ModelRepository;
use App\Repository\PriceRowRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Upsert строк цен по натуральному ключу (provider, model, validFrom).
 * Используется и при загрузке справочника, и при ретро-коррекции цен.
 *
 * Повторная запись с тем же содержимым ничего не меняет; при изменении
 * содержимого — обновляет существующую строку (id остаётся прежним).
 */
final class PriceCatalog
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelRepository $modelRepository,
        private readonly PriceRowRepository $priceRowRepository,
    ) {
    }

    /**
     * @return array{row: PriceRow, created: bool}
     *
     * @throws ImportException если модель (provider, model) отсутствует в справочнике
     */
    public function upsert(PriceSpec $spec): array
    {
        $model = $this->modelRepository->findOneByProviderAndCode($spec->provider, $spec->model);
        if ($model === null) {
            throw new ImportException(sprintf('Модель %s/%s не найдена в справочнике.', $spec->provider, $spec->model));
        }

        $row = $this->priceRowRepository->findByProviderModelAndValidFrom($spec->provider, $spec->model, $spec->validFrom);
        if ($row === null) {
            $row = new PriceRow($spec->unit, $spec->validFrom, $spec->currency, $spec->rates, $model);
            $this->em->persist($row);

            return ['row' => $row, 'created' => true];
        }

        if (!$row->sameContent($spec->unit, $spec->validFrom, $spec->currency, $spec->rates)) {
            $row->setUnit($spec->unit);
            $row->setCurrency($spec->currency);
            $row->setRates($spec->rates);
        }

        return ['row' => $row, 'created' => false];
    }
}
