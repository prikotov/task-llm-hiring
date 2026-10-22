<?php

namespace App\Service;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use DateTimeImmutable;
use DateTimeZone;

class BillingService
{
    private const SCALE = 16;
    private const REPORT_SCALE = 6;
    private const TOKEN_DIVISOR = '1000000';

    public function calculateCost(UsageEvent $event, Price $price): string
    {
        $cost = '0';

        if ($price->unit === 'per_1m_tokens') {
            $cost = bcadd($cost, bcmul((string)$event->tokensInput, $price->rateInput ?? '0', self::SCALE), self::SCALE);
            $cost = bcadd($cost, bcmul((string)$event->tokensOutput, $price->rateOutput ?? '0', self::SCALE), self::SCALE);
            $cost = bcadd($cost, bcmul((string)$event->tokensCacheRead, $price->rateCacheRead ?? '0', self::SCALE), self::SCALE);
            $cost = bcadd($cost, bcmul((string)$event->tokensCacheWrite, $price->rateCacheWrite ?? '0', self::SCALE), self::SCALE);
        } elseif ($price->unit === 'per_1m_tokens_unified') {
            $totalTokens = (string)($event->tokensInput + $event->tokensOutput + $event->tokensCacheRead + $event->tokensCacheWrite);
            $cost = bcmul($totalTokens, $price->rateAny ?? '0', self::SCALE);
        } else {
            throw new \InvalidArgumentException("Unsupported price unit: {$price->unit}");
        }

        $cost = bcdiv($cost, self::TOKEN_DIVISOR, self::SCALE);

        // Apply off-peak discount
        if ($this->isOffPeak($event->ts, $price->provider)) {
            $cost = bcmul($cost, $price->provider->offPeakMultiplier ?? '1', self::SCALE);
        }

        return $this->roundHalfUp($cost, self::REPORT_SCALE);
    }

    private function isOffPeak(DateTimeImmutable $ts, Provider $provider): bool
    {
        if (!$provider->offPeakWindowStart || !$provider->offPeakWindowEnd) {
            return false;
        }

        $time = $ts->setTimezone(new DateTimeZone('UTC'))->format('H:i');
        $start = $provider->offPeakWindowStart;
        $end = $provider->offPeakWindowEnd;

        if ($start < $end) {
            return $time >= $start && $time < $end;
        } else {
            // Window crosses midnight
            return $time >= $start || $time < $end;
        }
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        if (strpos($value, '.') === false) {
            return bcadd($value, '0', $precision);
        }
        
        $multiplier = bcpow('10', (string)$precision);
        $shifted = bcmul($value, $multiplier, self::SCALE);
        $rounded = bcadd($shifted, '0.5', self::SCALE);
        $truncated = bcdiv($rounded, '1', 0);
        
        return bcdiv($truncated, $multiplier, $precision);
    }
}
