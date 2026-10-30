<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Application\MoneyMath;
use App\Domain\Entity\Event;
use App\Domain\Entity\ModelPrice;
use App\Domain\Entity\Provider;

/**
 * The applicable price line for a model plus the provider's off-peak settings,
 * able to score a single event's cost.
 */
final class Pricing
{
    public function __construct(
        private ModelPrice $price,
        private Provider $provider,
    ) {
    }

    /**
     * Score one usage event in the billing (USD) currency.
     */
    public function cost(Event $event): string
    {
        $price = $this->price;
        $unit = $price->unit;
        $tokens = $event->tokenCounts();
        $perMillion = (string) 1_000_000;

        if ($price->isUnified()) {
            $total = $tokens['input'] + $tokens['output'] + $tokens['cache_read'] + $tokens['cache_write'];
            $raw = MoneyMath::multiply((string) $total, $price->anyRate);
            $cost = MoneyMath::divide($raw, $perMillion);
        } else {
            $sum = '0';
            if ($tokens['input'] > 0) {
                $sum = MoneyMath::add($sum, MoneyMath::multiply((string) $tokens['input'], $price->inputRate));
            }
            if ($tokens['output'] > 0) {
                $sum = MoneyMath::add($sum, MoneyMath::multiply((string) $tokens['output'], $price->outputRate));
            }
            if ($tokens['cache_read'] > 0) {
                $sum = MoneyMath::add($sum, MoneyMath::multiply((string) $tokens['cache_read'], $price->cacheReadRate));
            }
            if ($tokens['cache_write'] > 0) {
                $sum = MoneyMath::add($sum, MoneyMath::multiply((string) $tokens['cache_write'], $price->cacheWriteRate));
            }
            $cost = MoneyMath::divide($sum, $perMillion);
        }

        if ($this->isOffPeak($event)) {
            $cost = MoneyMath::multiply($cost, (string) $this->provider->offPeakMultiplier);
        }

        return MoneyMath::round6($cost);
    }

    private function isOffPeak(Event $event): bool
    {
        $start = $this->provider->offPeakStart;
        $end = $this->provider->offPeakEnd;
        if (null === $start || null === $end) {
            return false;
        }
        $hourMin = $event->timestamp->format('H:i');
        if ($hourMin >= $start && $hourMin < $end) {
            return true;
        }
        if ($start > $end) {
            // Window spans midnight.
            return $hourMin >= $start || $hourMin < $end;
        }

        return false;
    }
}
