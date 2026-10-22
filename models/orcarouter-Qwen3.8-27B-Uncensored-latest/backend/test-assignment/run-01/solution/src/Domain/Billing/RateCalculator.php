<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Catalog\PricingUnit;
use App\Domain\Usage\UsageEvent;

/**
 * Тарификация одного события одной строкой цен.
 *
 * Правила (TASK.md, п. 2 и 3):
 *  - per_1m_tokens:        cost = Σ tokens_вид × rate_вид / 1 000 000;
 *  - per_1m_tokens_unified: cost = Σ(все токены) × rate_any / 1 000 000;
 *  - оф-пик:                cost × multiplier;
 *  - единственное округление — на выходе, HALF_UP до 6 знаков.
 */
final class RateCalculator
{
    /**
     * Возвращает итоговую стоимость события (уже округлена до 6 знаков).
     */
    public static function eventCost(
        UsageEvent $event,
        string $unit,
        array $rates,
        ?OffPeakWindow $offPeak = null,
        ?string $offPeakMultiplier = null,
    ): string {
        $rates = array_map('strval', $rates);

        $cost = self::baseCost($event, PricingUnit::from($unit), $rates);

        if ($offPeak !== null && $offPeakMultiplier !== null && $offPeak->contains($event->ts)) {
            $cost = Money::mul($cost, $offPeakMultiplier);
        }

        return Money::roundHalfUpTo6($cost);
    }

    private static function baseCost(UsageEvent $event, PricingUnit $unit, array $rates): string
    {
        return match ($unit) {
            PricingUnit::PerUnified => Money::perMillion(
                $event->tokensInput + $event->tokensOutput + $event->tokensCacheRead + $event->tokensCacheWrite,
                $rates['any'],
            ),
            PricingUnit::PerToken => Money::add(
                Money::add(
                    Money::perMillion($event->tokensInput, $rates['input']),
                    Money::perMillion($event->tokensOutput, $rates['output']),
                ),
                Money::add(
                    Money::perMillion($event->tokensCacheRead, $rates['cache_read']),
                    Money::perMillion($event->tokensCacheWrite, $rates['cache_write']),
                ),
            ),
        };
    }
}
