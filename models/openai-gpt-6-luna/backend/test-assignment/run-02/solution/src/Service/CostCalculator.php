<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;

final class CostCalculator
{
    private const SCALE = 50;
    private const MILLION = '1000000';

    /**
     * @param array<string, mixed> $event
     * @param array<string, mixed> $price
     * @param array<string, mixed> $provider
     */
    public function calculate(array $event, array $price, array $provider): string
    {
        $cost = '0';
        if ($price['unit'] === 'per_1m_tokens') {
            foreach ([
                'input' => 'tokens_input',
                'output' => 'tokens_output',
                'cache_read' => 'tokens_cache_read',
                'cache_write' => 'tokens_cache_write',
            ] as $kind => $column) {
                $part = bcdiv(bcmul((string) $event[$column], (string) $price['rate_'.$kind], self::SCALE), self::MILLION, self::SCALE);
                $cost = bcadd($cost, $part, self::SCALE);
            }
        } else {
            $tokens = '0';
            foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $column) {
                $tokens = bcadd($tokens, (string) $event[$column], 0);
            }
            $cost = bcdiv(bcmul($tokens, (string) $price['rate_any'], self::SCALE), self::MILLION, self::SCALE);
        }

        if ($provider['off_peak_start'] !== null && $this->isOffPeak($event['occurred_at'], (string) $provider['off_peak_start'], (string) $provider['off_peak_end'])) {
            $cost = bcmul($cost, (string) $provider['off_peak_multiplier'], self::SCALE);
        }

        // Все значения неотрицательны: добавление половины младшего разряда реализует HALF_UP.
        return bcadd($cost, '0.0000005', 6);
    }

    private function isOffPeak(mixed $occurredAt, string $start, string $end): bool
    {
        $date = $occurredAt instanceof DateTimeImmutable
            ? $occurredAt
            : new DateTimeImmutable((string) $occurredAt);
        $time = $date->setTimezone(new \DateTimeZone('UTC'))->format('H:i');

        if ($start === $end) {
            return false;
        }

        return $start < $end
            ? ($time >= $start && $time < $end)
            : ($time >= $start || $time < $end);
    }
}
