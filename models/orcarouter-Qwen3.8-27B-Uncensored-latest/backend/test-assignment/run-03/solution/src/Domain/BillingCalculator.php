<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

/**
 * Расчёт стоимости одного события (фактически — одна функция предметной области).
 *
 * Правила (см. TASK.md):
 *  - per_1m_tokens:        cost = Σ tokens_вид × rate_вид / 1_000_000
 *  - per_1m_tokens_unified: cost = (Σ tokens) × rate_any / 1_000_000
 *  - оф-пик: умножение итога на множитель, если время суток в окне;
 *  - единственное округление — событийного итога, до 6 знаков, HALF_UP.
 *
 * Класс не знает о persistance — в него приходят только примитивы.
 */
final class BillingCalculator
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_PER_TOKEN_UNIFIED = 'per_1m_tokens_unified';

    private const DIVISOR = '1000000';

    /**
     * @param array<string, string> $rates десятичные ставки
     */
    public function calculate(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        string $unit,
        array $rates,
        ?OffPeak $offPeak,
        DateTimeImmutable $ts,
    ): string {
        $base = match ($unit) {
            self::UNIT_PER_TOKEN_UNIFIED => $this->unified(
                $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite,
                $rates['any'] ?? '0',
            ),
            self::UNIT_PER_TOKEN => $this->perKind(
                $tokensInput,
                $tokensOutput,
                $tokensCacheRead,
                $tokensCacheWrite,
                $rates,
            ),
            default => throw new \DomainException("unknown billing unit: {$unit}"),
        };

        if ($offPeak !== null && $offPeak->contains($ts)) {
            $base = Money::mul($base, $offPeak->multiplier());
        }

        return Money::roundHalfUp($base, 6);
    }

    private function perKind(
        int $in,
        int $out,
        int $cacheRead,
        int $cacheWrite,
        array $rates,
    ): string {
        $terms = [
            [(string) $in, (string) ($rates['input'] ?? '0')],
            [(string) $out, (string) ($rates['output'] ?? '0')],
            [(string) $cacheRead, (string) ($rates['cache_read'] ?? '0')],
            [(string) $cacheWrite, (string) ($rates['cache_write'] ?? '0')],
        ];

        $sum = '0';
        foreach ($terms as [$qty, $rate]) {
            $sum = Money::add($sum, Money::div(Money::mul($qty, $rate), self::DIVISOR));
        }

        return $sum;
    }

    private function unified(int $tokens, string $rate): string
    {
        return Money::div(Money::mul((string) $tokens, $rate), self::DIVISOR);
    }
}
