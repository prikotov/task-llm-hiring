<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PriceRow;
use App\Support\Decimal;
use App\Support\OffPeak;
use DateTimeImmutable;

/**
 * Чистый движок тарификации: знает только цифры и правила, не трогает БД.
 *
 * Ввод — данные уже выбранной строки цен + параметры оф-пика провайдера +
 * момент события; вывод — итоговая стоимость события в USD (6 знаков).
 *
 * Правила (см. TASK.md, «Правила тарификации»):
 *  - per_1m_tokens        : sum(tokens_v * rate_v) / 1_000_000
 *  - per_1m_tokens_unified: (sum всех токенов) * rate_any / 1_000_000
 *  - оф-пик               : итог события * multiplier, если ts попало в окно
 *  - итог                 : ровно одно округление до 6 знаков HALF_UP
 */
final class BillingEngine
{
    /**
     * @param array<string,string> $rates ставки выбранной строки цен
     */
    public function cost(
        string $unit,
        array $rates,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        ?string $offPeakStart,
        ?string $offPeakEnd,
        ?string $offPeakMultiplier,
        DateTimeImmutable $ts,
    ): string {
        $base = match ($unit) {
            PriceRow::UNIT_PER_TOKEN => $this->perToken($rates, $tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite),
            PriceRow::UNIT_UNIFIED => $this->unified($rates, $tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite),
            default => throw new \DomainException(sprintf('Неизвестная схема тарификации "%s"', $unit)),
        };

        if ($offPeakMultiplier !== null && OffPeak::isInWindow($ts, $offPeakStart, $offPeakEnd)) {
            $base = Decimal::mul($base, $offPeakMultiplier);
        }

        return Decimal::roundMoney($base);
    }

    /**
     * @param array<string,string> $rates
     */
    private function perToken(array $rates, int $ti, int $to, int $cr, int $cw): string
    {
        $sum = '0';
        foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
            $tokens = match ($kind) {
                'input' => $ti,
                'output' => $to,
                'cache_read' => $cr,
                'cache_write' => $cw,
            };
            $rate = (string) ($rates[$kind] ?? '0');
            if ($tokens === 0) {
                continue;
            }
            $sum = Decimal::add($sum, Decimal::mul((string) $tokens, $rate));
        }

        return Decimal::div1m($sum);
    }

    /**
     * @param array<string,string> $rates
     */
    private function unified(array $rates, int $ti, int $to, int $cr, int $cw): string
    {
        $rate = (string) ($rates['any'] ?? '0');
        $total = $ti + $to + $cr + $cw;

        return Decimal::div1m(Decimal::mul((string) $total, $rate));
    }
}
