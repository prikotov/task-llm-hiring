<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Money\Cost;
use App\Domain\Price\RateUnit;

/**
 * Расчёт стоимости события по строке цен + правилам оф-пик.
 *
 * cost = SUM( tokens_вид * rate_вид / 1_000_000 )
 *        * off_peak_multiplier (если ts в окне)
 * Итоговое событие округляется до 6 знаков HALF_UP (однажды).
 */
final class EventCostCalculator
{
    public function calculate(
        UsageEvent $event,
        array $rates,
        RateUnit $unit,
        ?\App\Domain\Provider\OffPeakWindow $offPeak,
    ): Cost {
        return match ($unit) {
            RateUnit::Per1mTokens => $this->perTokenBreakdown(
                $event,
                $rates['input'],
                $rates['output'],
                $rates['cache_read'],
                $rates['cache_write'],
            ),
            RateUnit::Per1mTokensUnified => $this->unified(
                $event,
                $rates['any'],
            ),
        };
    }

    private function perTokenBreakdown(UsageEvent $event, string $in, string $out, string $cr, string $cw): Cost
    {
        $cost = Cost::sum(
            Cost::perTokens($event->tokensInput(), $in),
            Cost::perTokens($event->tokensOutput(), $out),
            Cost::perTokens($event->tokensCacheRead(), $cr),
            Cost::perTokens($event->tokensCacheWrite(), $cw),
        );

        return $event->provider()->offPeakSettings() === null
            ? $cost
            : $event->provider()->offPeakSettings()->appliesToUtcTimestamp($cost, $event->ts());
    }

    private function unified(UsageEvent $event, string $any): Cost
    {
        $total = $event->tokensInput() + $event->tokensOutput() + $event->tokensCacheRead() + $event->tokensCacheWrite();
        $cost = Cost::perTokens($total, $any);

        return $event->provider()->offPeakSettings() === null
            ? $cost
            : $event->provider()->offPeakSettings()->appliesToUtcTimestamp($cost, $event->ts());
    }
}
