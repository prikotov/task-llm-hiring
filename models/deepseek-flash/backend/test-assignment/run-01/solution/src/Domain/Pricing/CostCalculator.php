<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use App\Domain\Usage\TokenUsage;
use DateTimeImmutable;

/**
 * The single place where an event's cost is calculated and rounded.
 */
final readonly class CostCalculator
{
    public const int EVENT_COST_SCALE = 6;

    public function calculate(
        TokenUsage $usage,
        RateCard $rateCard,
        ?OffPeakWindow $offPeak,
        DateTimeImmutable $occurredAt,
    ): Decimal {
        $cost = $rateCard->baseCost($usage);

        if ($offPeak !== null && $offPeak->contains($occurredAt)) {
            $cost = $cost->multiply($offPeak->multiplier());
        }

        return $cost->roundHalfUp(self::EVENT_COST_SCALE);
    }
}
