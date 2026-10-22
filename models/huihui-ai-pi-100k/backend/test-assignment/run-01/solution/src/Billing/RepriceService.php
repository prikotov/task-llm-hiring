<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Decimal;
use App\Domain\EventStatus;
use App\Entity\PriceCorrection;
use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use App\Import\DirectoryPersister;
use App\Import\PriceDoc;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус):
 * 1) upsert исправленных строк цен по (provider, model, valid_from);
 * 2) пересчёт событий, тарифицированных заменёнными строками;
 * 3) журнал корректировок (price_corrections) — перезапись стоимости
 *    с сохранением старой и новой суммы.
 *
 * Итоговый app:billing:report после reprice показывает новые суммы без
 * задвоений: стоимость события хранится в одном месте и заменяется,
 * а не добавляет вторую запись.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DirectoryPersister $persister,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * @throws \App\Import\ImportException
     * @throws \DomainException
     */
    public function reprice(PriceDoc $doc): RepriceResult
    {
        return $this->em->wrapInTransaction(function () use ($doc): RepriceResult {
            $applied = $this->persister->apply($doc);
            $rows = $applied['rows'];

            if ($rows === []) {
                return new RepriceResult(0, Decimal::zero());
            }

            $rowIds = array_map(static fn (PriceRow $row): int => (int) $row->getId(), $rows);
            $placeholders = implode(', ', array_fill(0, count($rowIds), '?'));

            /** @var list<int|string> $eventIds */
            $eventIds = $this->em->getConnection()->fetchFirstColumn(
                'SELECT id FROM usage_events WHERE status = ? AND price_row_id IN (' . $placeholders . ') ORDER BY id ASC',
                array_merge([EventStatus::Rated->value], $rowIds),
            );

            $delta = Decimal::zero();
            $changed = 0;

            foreach ($eventIds as $eventId) {
                /** @var UsageEvent|null $event */
                $event = $this->em->find(UsageEvent::class, (int) $eventId);
                if ($event === null) {
                    continue;
                }

                $row = $event->getPriceRow();
                if ($row === null) {
                    continue;
                }

                $oldCost = Decimal::of((string) $event->getCost());
                $newCost = $this->calculator->priceCost($row, $event->getTokenUsage(), $event->getTs(), $event->getProvider()->offPeak())
                    ->roundHalfUp(6);

                if ($newCost->equals($oldCost)) {
                    continue; // стоимость не изменилась - журнал не засоряем
                }

                $event->setCost($newCost);
                $changed++;
                $this->em->persist(new PriceCorrection($event, $row, $oldCost, $newCost));

                $delta = $delta->add($newCost->sub($oldCost));
            }

            $this->em->flush();

            return new RepriceResult($changed, $delta->roundHalfUp(6));
        });
    }
}
