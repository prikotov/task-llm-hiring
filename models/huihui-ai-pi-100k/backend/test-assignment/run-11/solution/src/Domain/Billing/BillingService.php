<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\StoredPriceRow;
use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageEventRepository;

/**
 * Ядро тарификации (TASK.md, раздел «Правила тарификации»):
 *  - выбор строки цен — max `validFrom` ≤ `ts` (включительно);
 *  - точная стоимость по схеме (`per_1m_tokens` / `per_1m_tokens_unified`);
 *  - оф-пик — умножение на `multiplier` при попадании в окно провайдера;
 *  - единственное округление — HALF_UP до 6 знаков (TASK.md п. 4).
 *
 * Идемпотентность записи — в persistence-слое (RatedEventStore).
 */
final class BillingService
{
    public function __construct(
        private readonly PriceCatalog $catalog,
        private readonly RatedEventStore $store,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * Тарифицирует все ещё не рассчитанные события (включая повторные
     * попытки unrated; цены могли подтянуться позже — TASK.md п. 7).
     */
    public function rateAllUnrated(): RateResult
    {
        $pending = $this->events->findUnrated();

        $ratedCount = 0;
        $unrated = [];
        foreach ($pending as $event) {
            $priceRow = $this->catalog->findPriceRowFor($event->provider, $event->model, $event->ts);
            if ($priceRow === null) {
                $unrated[] = $event;
                continue;
            }

            $cost = $this->calculateCost($event, $this->toDomainRow($priceRow));
            $this->store->saveRated($event, $cost);
            $ratedCount++;
        }

        return new RateResult($ratedCount, $unrated);
    }

    /**
     * Стоимость события по строке цен (используется и при reprice).
     *
     * @throws \App\Domain\DomainException
     */
    public function calculateCost(UsageEvent $event, PriceRow $row): Cost
    {
        $base = $row->unit->calculateCost($event->tokens, $row->rates);

        $provider = $this->catalog->findProvider($event->provider);
        if ($provider !== null && $provider->offPeak !== null) {
            $offPeak = $provider->offPeak;
            if ($offPeak->appliesAt($event->ts)) {
                $base = $base->multiply($offPeak->multiplier);
            }
        }

        // Единственное округление: стоимость события, HALF_UP до 6 знаков.
        return Cost::roundHalfUp6($base);
    }

    private function toDomainRow(StoredPriceRow $entity): PriceRow
    {
        return $entity->toDomain();
    }
}
