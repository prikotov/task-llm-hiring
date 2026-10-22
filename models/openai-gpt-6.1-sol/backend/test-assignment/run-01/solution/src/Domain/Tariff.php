<?php

declare(strict_types=1);

namespace App\Domain;

final class Tariff
{
    public const KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    /** Все операции до единственного округления точные, включая произвольную длину multiplier. */
    public function calculate(array $tokens, string $unit, array $rates, ?array $offPeak, string $timestamp): string
    {
        $sum = '0';
        foreach (self::KINDS as $kind) {
            $sum = bcadd($sum, bcmul((string) $tokens[$kind], $rates[$unit === 'per_1m_tokens_unified' ? 'any' : $kind], 6), 6);
        }
        $cost = bcdiv($sum, '1000000', 12);
        if ($offPeak !== null && $this->inWindow(substr($timestamp, 11, 8), $offPeak['window_start'], $offPeak['window_end'])) {
            $scale = strlen(explode('.', $offPeak['multiplier'])[1] ?? '');
            $cost = bcmul($cost, $offPeak['multiplier'], 12 + $scale);
        }

        return bcadd($cost, '0.0000005', 6);
    }

    public function inWindow(string $time, string $start, string $end): bool
    {
        $start .= ':00';
        $end .= ':00';

        return $start <= $end
            ? $time >= $start && $time < $end
            : $time >= $start || $time < $end;
    }
}
