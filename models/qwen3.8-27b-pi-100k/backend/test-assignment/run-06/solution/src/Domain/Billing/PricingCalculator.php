<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use DateTimeImmutable;

/**
 * Чистая арифметика стоимости события.
 *
 * Все вычисления — bcmath (никакого float): промежуточные значения
 * без округления (масштаб 24), ровно одно округление итоговой стоимости
 * события до 6 знаков HALF_UP.
 */
final class PricingCalculator
{
    private const SCALE = 24;

    /**
     * @param array<string, string> $rates десятичные строки: input/output/
     *                                    cache_read/cache_write либо any
     */
    public function eventCost(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        PriceUnit $unit,
        array $rates,
        ?OffPeakWindow $offPeak,
        DateTimeImmutable $tsUtc,
    ): string {
        $base = match ($unit) {
            PriceUnit::Per1MToken => $this->perTokenCost(
                $tokensInput,
                $rates['input'],
                $tokensOutput,
                $rates['output'],
                $tokensCacheRead,
                $rates['cache_read'],
                $tokensCacheWrite,
                $rates['cache_write'],
            ),
            PriceUnit::Per1MTokenUnified => $this->per1M(
                $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite,
                $rates['any'],
            ),
        };

        if ($offPeak !== null && $offPeak->contains($tsUtc)) {
            $base = bcmul($base, $offPeak->multiplier, self::SCALE);
        }

        return self::roundHalfUp6($base);
    }

    /**
     * Округление до 6 знаков HALF_UP (x >= 0): floor(x * 10^6 + 0.5) / 10^6.
     */
    public static function roundHalfUp6(string $value): string
    {
        $shifted = bcadd($value, '0.0000005', self::SCALE);

        return bcdiv($shifted, '1', 6);
    }

    /**
     * Форматирует десятичную строку ровно в 6 знаков (без изменения значения).
     */
    public static function format6(string $value): string
    {
        return bcadd($value, '0', 6);
    }

    private function perTokenCost(
        int $input,
        string $rateInput,
        int $output,
        string $rateOutput,
        int $cacheRead,
        string $rateCacheRead,
        int $cacheWrite,
        string $rateCacheWrite,
    ): string {
        return bcadd(
            bcadd($this->per1M($input, $rateInput), $this->per1M($output, $rateOutput), self::SCALE),
            bcadd($this->per1M($cacheRead, $rateCacheRead), $this->per1M($cacheWrite, $rateCacheWrite), self::SCALE),
            self::SCALE,
        );
    }

    private function per1M(int $tokens, string $rate): string
    {
        if ($tokens === 0) {
            return '0';
        }

        return bcdiv(bcmul((string) $tokens, $rate, self::SCALE), '1000000', self::SCALE);
    }
}
