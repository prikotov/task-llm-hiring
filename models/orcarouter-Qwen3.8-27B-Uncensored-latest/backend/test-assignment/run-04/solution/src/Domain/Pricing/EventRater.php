<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Money;

/**
 * Pure rating rules: scheme cost, off-peak application, the single final
 * HALF_UP rounding to 6 digits.
 */
final class EventRater
{
    public function rate(UsageRecord $usage, PriceEntry $price, ?OffPeak $offPeak): string
    {
        $cost = $price->baseCost($usage);

        if ($offPeak !== null && $offPeak->contains($usage->ts)) {
            $cost = $offPeak->apply($cost);
        }

        return Money::roundHalfUp6($cost);
    }
}
