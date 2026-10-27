<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Domain\OffPeakWindow;
use Doctrine\DBAL\Connection;

/**
 * Core billing engine that computes the cost of a usage event
 * based on the applicable price line and provider off-peak rules.
 */
final class BillingEngine
{
    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * Compute the cost for a usage event and return [cost_string, price_valid_from]
     * or [null, null] if no applicable price line is found.
     *
     * @return array{0: string|null, 1: string|null}
     */
    public function computeCost(string $providerCode, string $modelCode, string $ts, array $tokens): array
    {
        // Find the applicable price line: largest valid_from <= ts
        $price = $this->db->fetchAssociative(
            'SELECT provider_code, model_code, valid_from, currency, unit, rates
             FROM prices
             WHERE provider_code = ? AND model_code = ? AND valid_from <= ?::timestamptz
             ORDER BY valid_from DESC
             LIMIT 1',
            [$providerCode, $modelCode, $ts]
        );

        if (!$price) {
            return [null, null];
        }

        // Decode rates
        $rates = json_decode($price['rates'], true);
        $unit = $price['unit'];

        // Compute base cost
        $baseCost = $this->computeBaseCost($unit, $rates, $tokens);

        // Check off-peak
        $provider = $this->db->fetchAssociative(
            'SELECT off_peak_window_start, off_peak_window_end, off_peak_multiplier
             FROM providers WHERE code = ?',
            [$providerCode]
        );

        $cost = $baseCost;
        if ($provider && $provider['off_peak_window_start'] !== null) {
            $offPeak = new OffPeakWindow(
                $provider['off_peak_window_start'],
                $provider['off_peak_window_end'],
                $provider['off_peak_multiplier']
            );

            $timeOfDay = $this->extractTimeOfDay($ts);
            if ($offPeak->contains($timeOfDay)) {
                $cost = Money::mul($cost, $offPeak->getMultiplier());
            }
        }

        // Round to 6 decimal places HALF_UP
        $rounded = Money::roundHalfUp($cost, 6);

        return [$rounded, $price['valid_from']];
    }

    /**
     * @param array{input:int, output:int, cache_read:int, cache_write:int} $tokens
     */
    private function computeBaseCost(string $unit, array $rates, array $tokens): string
    {
        $divisor = '1000000';

        if ($unit === 'per_1m_tokens') {
            $cost = '0';
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['input'], $rates['input'] ?? '0'), $divisor));
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['output'], $rates['output'] ?? '0'), $divisor));
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['cache_read'], $rates['cache_read'] ?? '0'), $divisor));
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['cache_write'], $rates['cache_write'] ?? '0'), $divisor));
            return $cost;
        }

        if ($unit === 'per_1m_tokens_unified') {
            $totalTokens = $tokens['input'] + $tokens['output'] + $tokens['cache_read'] + $tokens['cache_write'];
            return Money::div(Money::mul((string) $totalTokens, $rates['any'] ?? '0'), $divisor);
        }

        throw new \InvalidArgumentException("Unknown unit: {$unit}");
    }

    /**
     * Extract time-of-day in minutes from an ISO-8601 UTC timestamp.
     */
    private function extractTimeOfDay(string $ts): int
    {
        // ts is like "2026-09-15T11:00:00Z"
        // Use DateTime to parse reliably
        $dt = new \DateTimeImmutable($ts, new \DateTimeZone('UTC'));
        return (int) $dt->format('G') * 60 + (int) $dt->format('i');
    }
}
