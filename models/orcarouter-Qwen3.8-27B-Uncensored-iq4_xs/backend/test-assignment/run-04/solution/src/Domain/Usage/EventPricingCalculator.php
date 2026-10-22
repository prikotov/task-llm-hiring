<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Catalog\OffPeakWindow;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\PricingUnit;
use App\Domain\Money\Money;

/**
 * Чистый расчёт стоимости события (без БД):
 *
 *  - per_1m_tokens:          cost = Σ tokens_вид × rate_вид / 1 000 000
 *  - per_1m_tokens_unified:  cost = (tokens_input + tokens_output
 *                                     + tokens_cache_read + tokens_cache_write) × rate_any / 1 000 000
 *
 * Промежуточные вычисления — точные (без округления; масштаб ≥ 16).
 * Если время суток события (UTC) в оф-пиковом окне провайдера —
 * стоимость всего события умножается на multiplier.
 * Единственное округление — итог события до 6 знаков HALF_UP.
 */
final class EventPricingCalculator
{
    private const TOKENS_PER_UNIT = 1_000_000;

    public function computeEventCost(UsageEvent $event, PriceRow $price, ?OffPeakWindow $offPeak): Money
    {
        $gross = $this->grossWithoutUnitScale($event, $price);
        $cost = $gross->dividedBy(self::TOKENS_PER_UNIT);

        if ($offPeak !== null && $offPeak->appliesAtUtc($event->ts())) {
            $cost = $cost->multipliedBy($offPeak->multiplier);
        }

        return $cost->roundedHalfUpToFixed();
    }

    /**
     * Σ(токенов × цены) без деления на размер единицы — Money в полной
     * десятичной точности, без округления.
     */
    private function grossWithoutUnitScale(UsageEvent $event, PriceRow $price): Money
    {
        return match ($price->unit()) {
            PricingUnit::Per1MTokens => $this->sumPerTokenKinds($event, $price),
            PricingUnit::Per1MTokensUnified => Money::exact((string) $event->totalTokens())
                ->multipliedBy($price->rate('any')),
        };
    }

    private function sumPerTokenKinds(UsageEvent $event, PriceRow $price): Money
    {
        $sum = Money::zero();
        foreach (TokenKind::all() as $kind) {
            $rate = $price->rate($kind->value);
            if ($rate === null) {
                throw new \LogicException(sprintf(
                    'Price row %s (unit %s) has no rate for token kind "%s".',
                    (string) $price->id(),
                    $price->unit()->value,
                    $kind->value,
                ));
            }

            $sum = $sum->add(
                Money::exact((string) $event->tokenCount($kind))->multipliedBy($rate),
            );
        }

        return $sum;
    }
}
