<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;

class CostCalculator
{
    private const SCALE = 16;
    private const ONE_MILLION = '1000000';

    public function calculate(UsageEvent $event, Price $price, Provider $provider): string
    {
        $tokensInput = (string)$event->getTokensInput();
        $tokensOutput = (string)$event->getTokensOutput();
        $tokensCacheRead = (string)$event->getTokensCacheRead();
        $tokensCacheWrite = (string)$event->getTokensCacheWrite();

        $unit = $price->getUnit();
        if ($unit === 'per_1m_tokens') {
            $cost = '0';
            $cost = $this->addMul($cost, $tokensInput, $price->getInputRate());
            $cost = $this->addMul($cost, $tokensOutput, $price->getOutputRate());
            $cost = $this->addMul($cost, $tokensCacheRead, $price->getCacheReadRate());
            $cost = $this->addMul($cost, $tokensCacheWrite, $price->getCacheWriteRate());
        } elseif ($unit === 'per_1m_tokens_unified') {
            $totalTokens = bcadd($tokensInput, $tokensOutput, self::SCALE);
            $totalTokens = bcadd($totalTokens, $tokensCacheRead, self::SCALE);
            $totalTokens = bcadd($totalTokens, $tokensCacheWrite, self::SCALE);
            $cost = $this->mulDiv($totalTokens, $price->getAnyRate());
        } else {
            $cost = '0';
        }

        // off-peak
        if ($provider->hasOffPeak()) {
            $ts = $event->getTs();
            $time = $ts->format('H:i');
            $start = $provider->getOffPeakStart();
            $end = $provider->getOffPeakEnd();
            if ($this->isTimeInWindow($time, $start, $end)) {
                $multiplier = $provider->getOffPeakMultiplier();
                $cost = bcmul($cost, $multiplier, self::SCALE);
            }
        }

        // round to 6 decimals half up
        return $this->roundHalfUp($cost, 6);
    }

    private function addMul(string $sum, string $tokens, ?string $rate): string
    {
        if ($rate === null || $tokens === '0') {
            return $sum;
        }
        $part = $this->mulDiv($tokens, $rate);
        return bcadd($sum, $part, self::SCALE);
    }

    private function mulDiv(string $tokens, ?string $rate): string
    {
        if ($rate === null) {
            return '0';
        }
        $numerator = bcmul($tokens, $rate, self::SCALE);
        return bcdiv($numerator, self::ONE_MILLION, self::SCALE);
    }

    private function isTimeInWindow(string $time, string $start, string $end): bool
    {
        $t = $this->timeToMinutes($time);
        $s = $this->timeToMinutes($start);
        $e = $this->timeToMinutes($end);
        if ($s <= $e) {
            return $t >= $s && $t < $e;
        }
        // crosses midnight
        return $t >= $s || $t < $e;
    }

    private function timeToMinutes(string $t): int
    {
        [$h, $m] = explode(':', $t);
        return ((int)$h) * 60 + ((int)$m);
    }

    private function roundHalfUp(string $value, int $decimals): string
    {
        $factor = bcpow('10', (string)$decimals);
        $scaled = bcmul($value, $factor, self::SCALE);
        $scaledPlusHalf = bcadd($scaled, '0.5', self::SCALE);
        $intPart = bcadd($scaledPlusHalf, '0', 0); // truncate
        $rounded = bcdiv($intPart, $factor, $decimals);
        // format with exactly $decimals digits
        if (strpos($rounded, '.') === false) {
            $rounded .= '.' . str_repeat('0', $decimals);
        } else {
            [$int, $frac] = explode('.', $rounded);
            $frac = str_pad($frac, $decimals, '0', STR_PAD_RIGHT);
            $frac = substr($frac, 0, $decimals);
            $rounded = $int . '.' . $frac;
        }
        return $rounded;
    }
}
