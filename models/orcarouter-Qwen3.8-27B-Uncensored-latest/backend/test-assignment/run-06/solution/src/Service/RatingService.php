<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\MoneyMath;
use App\Domain\PricingEngine;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Repository\PriceRowRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий и ретро-коррекция цен.
 *
 * Правила (TASK.md, «Правила тарификации»):
 *  1. цена — строка (provider, model) с наибольшим valid_from <= ts;
 *  2. схема per_1m_tokens / per_1m_tokens_unified;
 *  3. оф-пик провайдера: множитель, если время суток ts (UTC) в окне [start, end);
 *  4. без округления до конца; единственное округление — итог События,
 *     6 знаков, HALF_UP; хранится точное целое (микро юниты);
 *  7. unrated-события повторно тарифицируются при каждом rate.
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly PriceRowRepository $prices,
        private readonly ProviderRepository $providers,
        private readonly PricingEngine $pricingEngine,
    ) {
    }

    /**
     * Тарифицирует все ещё не рассчитанные события.
     *
     * @return array{rated: int, unrated: UsageEvent[]}
     */
    public function ratePending(): array
    {
        $unrated = $this->events->findUnrated();
        $providerCache = [];
        $pricesCache = [];
        $ratedCount = 0;
        $stillUnrated = [];

        foreach ($unrated as $event) {
            $price = $this->pickPrice($event->provider, $event->model, $event->ts, $pricesCache);
            if ($price === null) {
                $stillUnrated[] = $event;
                continue;
            }

            $provider = $providerCache[$event->provider] ??= $this->providers->findByCode($event->provider);

            $event->costMicros = MoneyMath::roundToMicros(
                $this->costOf($event, $price, $provider),
            );
            $event->status = UsageEvent::STATUS_RATED;
            $event->appliedPriceId = $price->id;
            $event->ratedAt = new \DateTimeImmutable();
            ++$ratedCount;
        }

        $this->em->flush();

        return ['rated' => $ratedCount, 'unrated' => $stillUnrated];
    }

    /**
     * Ретро-коррекция: пересчёт событий, тарифицированных строками цен,
     * затронутыми коррекцией.
     *
     * @return array{recomputed: int, deltaMicros: int}
     */
    public function repriceByPriceIds(array $priceIds): array
    {
        if ($priceIds === []) {
            return ['recomputed' => 0, 'deltaMicros' => 0];
        }

        $affected = $this->events->findRatedByPriceIds($priceIds);
        $providerCache = [];
        $pricesCache = [];
        $delta = 0;

        foreach ($affected as $event) {
            $price = $this->pickPrice($event->provider, $event->model, $event->ts, $pricesCache);
            if ($price === null) {
                // Строка цен была удалена (не должно случаться при upsert) —
                // событие остаётся как есть.
                continue;
            }

            $provider = $providerCache[$event->provider] ??= $this->providers->findByCode($event->provider);

            $old = $event->costMicros;
            $new = MoneyMath::roundToMicros($this->costOf($event, $price, $provider));
            $event->costMicros = $new;
            $event->appliedPriceId = $price->id;
            $event->ratedAt = new \DateTimeImmutable();
            $delta += $new - $old;
        }

        $this->em->flush();

        return ['recomputed' => count($affected), 'deltaMicros' => $delta];
    }

    /**
     * Выбор строки цен (п. 1): наибольшее valid_from <= ts события.
     *
     * @param array<string, PriceRow[]> $pricesCache
     */
    private function pickPrice(string $provider, string $model, \DateTimeImmutable $ts, array &$pricesCache): ?PriceRow
    {
        $key = $provider.'\x1F'.$model;
        if (!isset($pricesCache[$key])) {
            $pricesCache[$key] = $this->prices->findByModel($provider, $model);
        }

        foreach ($pricesCache[$key] as $price) {
            if ($price->validFrom <= $ts) {
                return $price; // отсортировано по validFrom DESC
            }
        }

        return null;
    }

    private function costOf(UsageEvent $event, PriceRow $price, ?Provider $provider): string
    {
        $offStart = $provider?->hasOffPeak() ? $provider->offPeakStart : null;
        $offEnd = $provider?->hasOffPeak() ? $provider->offPeakEnd : null;
        $offMultiplier = $provider?->hasOffPeak() ? $provider->offPeakMultiplier : null;

        return $this->pricingEngine->eventCostBeforeRounding(
            $event->tokensInput,
            $event->tokensOutput,
            $event->tokensCacheRead,
            $event->tokensCacheWrite,
            $price->unit,
            $price->rates(),
            $offStart,
            $offEnd,
            $offMultiplier,
            $event->ts->format('H:i'),
        );
    }
}
