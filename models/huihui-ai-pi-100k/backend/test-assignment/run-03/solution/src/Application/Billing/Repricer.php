<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Exception\FileFormatException;
use App\Application\Pricing\CatalogImporter;
use App\Application\Pricing\PriceDocument;
use App\Domain\Common\Decimal;
use App\Domain\Pricing\CostCalculator;
use App\Persistence\Entity\Provider;
use App\Persistence\Repository\PriceRowRepository;
use App\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус TASK.md):
 *  1) исправленные строки цен применяются upsert-ом по
 *     (provider, model, valid_from) — провайдер и модель должны уже
 *     существовать в базе (секции providers/models в файле коррекции
 *     могут отсутствовать);
 *  2) пересчитываются события, тарифицированные именно этими строками;
 *  3) итог — число пересчитанных событий и дельта (новая сумма − старая),
 *     итоговый отчёт показывает пересчитанные суммы без задвоений.
 *
 * Механизм — прозрачная перезапись billed_cost события (документируется
 * в PR_DESCRIPTION.md): событие не дублируется, а его единственная
 * стоимость заменяется новой.
 */
final class Repricer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogImporter $catalog,
        private readonly UsageEventRepository $events,
        private readonly PriceRowRepository $priceRows,
        private readonly CostCalculator $calculator,
    ) {
    }

    /** @return array{events: int, delta: Decimal} */
    public function reprice(PriceDocument $document): array
    {
        if ($document->priceRows === []) {
            throw FileFormatException::invalidFile('reprice', 'файл коррекции не содержит ни одной строки цен');
        }

        // Шаг 1: применяем документ (провайдеры, модели, строки цен — upsert).
        $this->catalog->import($document);
        // Отделяем шаг 1 от шага 2: дальше работаем с чистым состоянием сущностей.
        $this->em->clear();

        // Шаг 2: события, тарифицированные заменёнными строками.
        $rowIds = [];
        foreach ($document->priceRows as $spec) {
            $row = $this->priceRows->findFor($spec->providerCode, $spec->modelCode, $spec->validFrom);
            if ($row !== null) {
                $rowIds[$row->id] = true;
            }
        }

        if ($rowIds === []) {
            return ['events' => 0, 'delta' => Decimal::zero()];
        }

        return $this->em->wrapInTransaction(function () use ($rowIds): array {
            $affected = $this->events->findRatedByPriceRowIds(array_keys($rowIds));
            $delta = Decimal::zero();

            foreach ($affected as $event) {
                $row = $this->em->createQuery(
                    'SELECT pr FROM '.\App\Persistence\Entity\PriceRow::class.' pr WHERE pr.id = :id',
                )->setParameter('id', $event->priceRowId)->getOneOrNullResult();
                if ($row === null) {
                    continue;
                }

                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $event->provider]);
                $newCost = $this->calculator->eventCost(
                    $event->tokenUsage(),
                    $row->toValueObject(),
                    $event->tsUtc(),
                    $provider?->offPeak(),
                );

                $delta = $delta->add(Decimal::of($newCost)->sub(Decimal::of($event->billedCost ?? '0')));
                $event->billedCost = $newCost;
            }

            $this->em->flush();

            return ['events' => count($affected), 'delta' => $delta];
        });
    }
}
