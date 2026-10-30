<?php

declare(strict_types=1);

namespace App\Billing\Engine;

use App\Billing\Dto\TokenCounts;
use App\Billing\Entity\EntityId;
use App\Billing\Entity\RatedEvent;
use App\Billing\Entity\UsageEvent;
use App\Billing\Store\Store;

/**
 * Основной расчёт: тарифицирует все ещё не оценённые события.
 * Для unrated-события в stdout (команда rate) печать — на стороне команды.
 */
final class RateService
{
    public function __construct(
        private readonly Store $store,
        private readonly EventCostCalculator $calculator,
    ) {}

    /**
     * @return array{rated:list<array{event:string, ts:string, cost:string}>, unrated:list<array{id:string, ts:string, provider:string}>}
     */
    public function rateAll(): array
    {
        $result = ['rated' => [], 'unrated' => []];

        /** @var list<UsageEvent> $pending */
        $pending = $this->store->allUnratedEvents();
        foreach ($pending as $event) {
            $priceRow = $this->store->pricedRowForEvent($event->providerCode, $event->modelCode, $event->ts);
            if ($priceRow === null) {
                $result['unrated'][] = [
                    'id' => $event->id,
                    'ts' => $event->ts->format('c'),
                    'provider' => $event->providerCode,
                ];
                continue;
            }

            $tokens = TokenCounts::fromArray($event->tokens);
            $cost = $this->calculator->computeEventCost(
                $event->providerCode,
                $event->ts,
                $tokens,
                $priceRow,
            );

            $rated = new RatedEvent($event->id);
            $rated->priceRowId = $priceRow->id;
            $rated->eventTs = $event->ts;
            $rated->user = $event->user;
            $rated->tokens = $event->tokens;
            $rated->costUsd = $cost->toString();
            $rated->createdAt = new \DateTimeImmutable();
            $rated->repricedAt = null;

            $this->store->upsertRatedEvent($rated);
            $this->store->markUsageEventRated($event->id);

            $result['rated'][] = [
                'event' => $event->id,
                'ts' => $event->ts->format('c'),
                'cost' => $cost->toString(),
            ];
        }

        return $result;
    }
}
