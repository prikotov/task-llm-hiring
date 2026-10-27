<?php

declare(strict_types=1);

namespace App\Billing\Engine;

use App\Billing\Dto\TokenCounts;
use App\Billing\Entity\RepriceLog;
use App\Billing\Entity\UsageEvent;
use App\Billing\Store\Store;
use App\Domain\Billing\Money;

/**
 * Бонус: пересчёт стоимости событий, тарифицированных заменёнными строками цен.
 *
 * Механизм:
 *   - для каждой затронутой price row ищем rated events, которые
 *     тарифицировались именно по этой строке (price_row_id);
 *   - пере-вычисляем стоимость по новой rate;
 *   - обновляем rated_events.cost_usd и repriced_at;
 *   - сохраняем RepriceLog с affected_units, событиями и дельтами.
 */
final class RepriceService
{
    public function __construct(
        private readonly Store $store,
        private readonly EventCostCalculator $calculator,
    ) {}

    /**
     * @param list<string> $rowIds
     */
    public function reprice(array $rowIds): int
    {
        return $this->repricePriceRows($rowIds);
    }

    /**
     * @param list<string> $rowIds
     *
     * @return array{events:int, old_usd:string, new_usd:string, delta_usd:string}
     */
    public function repricePriceRows(array $rowIds): array
    {
        $sumOld = Money::zero();
        $sumNew = Money::zero();
        $count = 0;

        /** @var list<\App\Billing\Entity\RatedEvent> $events */
        $events = $this->store->ratedEventsPricedByRows($rowIds);

        foreach ($events as $rated) {
            $row = $this->store->priceRow($rated->priceRowId);
            if ($row === null) {
                continue;
            }

            $usage = $this->store->usageEvent($rated->eventId);
            if ($usage === null) {
                continue;
            }

            $tokens = TokenCounts::fromArray($usage->tokens);

            $newCost = $this->calculator->computeEventCost(
                $usage->providerCode,
                $usage->ts,
                $tokens,
                $row,
            );

            $oldCost = Money::from($rated->costUsd);
            $sumOld = $sumOld->add($oldCost);
            $sumNew = $sumNew->add($newCost);
            $count++;

            $rated->costUsd = $newCost->toString();
            $rated->repricedAt = new \DateTimeImmutable();
        }

        $delta = $sumNew->subtract($sumOld);

        $this->store->flush();

        $log = new RepriceLog();
        $log->appliedAt = new \DateTimeImmutable();
        $log->affectedUnits = $rowIds;
        $log->events = $count;
        $log->oldSumUsd = $sumOld->toString();
        $log->newSumUsd = $sumNew->toString();
        $log->deltaUsd = $delta->toString();
        $this->store->persistRepriceLog($log);

        return [
            'events' => $count,
            'old_usd' => $sumOld->toString(),
            'new_usd' => $sumNew->toString(),
            'delta_usd' => $delta->toString(),
        ];
    }
}
