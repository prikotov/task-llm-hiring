<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;

class BillingRateCalculator
{
    public function calculateCost(UsageEvent $event, Price $price, ?Provider $provider): string
    {
        $cost = '0';

        if ($price->getUnit() === 'per_1m_tokens_unified') {
            $allTokens = $event->getTokensInput() + $event->getTokensOutput() + $event->getTokensCacheRead() + $event->getTokensCacheWrite();
            $rate = $price->getRates()['any'];
            $cost = bcmul((string)$allTokens, $rate, 16);
        } else {
            $rates = $price->getRates();
            $cost = bcadd($cost, bcmul((string)$event->getTokensInput(), $rates['input'] ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul((string)$event->getTokensOutput(), $rates['output'] ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul((string)$event->getTokensCacheRead(), $rates['cache_read'] ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul((string)$event->getTokensCacheWrite(), $rates['cache_write'] ?? '0', 16), 16);
        }

        $cost = bcdiv($cost, '1000000', 16);

        if ($provider && $provider->getOffPeak()) {
            $offPeak = $provider->getOffPeak();
            if ($this->isOffPeak($event->getTs(), $offPeak)) {
                $cost = bcmul($cost, $offPeak['multiplier'], 16);
            }
        }

        return $this->roundHalfUp($cost, 6);
    }

    private function isOffPeak(\DateTimeImmutable $ts, array $offPeak): bool
    {
        $time = $ts->format('H:i');
        $start = $offPeak['window_start'];
        $end = $offPeak['window_end'];

        if ($start < $end) {
            return $time >= $start && $time < $end;
        } else {
            return $time >= $start || $time < $end;
        }
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        $multiplier = bcpow('10', (string)$precision);
        $shifted = bcmul($value, $multiplier, 16);
        $rounded = bcadd($shifted, '0.5', 16);
        $integerPart = bcadd($rounded, '0', 0);
        return bcdiv($integerPart, $multiplier, $precision);
    }
}
