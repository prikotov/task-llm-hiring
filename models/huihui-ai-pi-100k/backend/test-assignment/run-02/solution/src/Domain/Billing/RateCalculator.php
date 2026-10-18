<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Shared\Decimal;

/**
 * Расчёт стоимости единого события (чистая доменная функция, без ORM).
 *
 * Правила (из TASK.md):
 *  - per_1m_tokens:      cost = Σ tokens_вид × rate_вид / 1 000 000;
 *  - per_1m_tokens_unified: cost = Σ_вид tokens_вид × rate_any / 1 000 000;
 *  - оф-пик: если момент события (UTC) в окне — стоимость умножается на множитель;
 *  - точные промежуточные вычисления (bcmath, масштаб 32), ровно одно
 *    округление итога до 6 знаков HALF_UP.
 */
final class RateCalculator
{
    private const TOKEN_DENOMINATOR = '1000000';

    public function eventCost(
        TokenCounts $tokens,
        PriceRow $row,
        ?OffPeakPolicy $offPeak,
        int $minuteOfDayUtc,
    ): string {
        $base = match ($row->getUnit()) {
            PriceUnit::Per1mTokens => Decimal::sum([
                self::perMillion((string) $tokens->input, $row->getInputRate()),
                self::perMillion((string) $tokens->output, $row->getOutputRate()),
                self::perMillion((string) $tokens->cacheRead, $row->getCacheReadRate()),
                self::perMillion((string) $tokens->cacheWrite, $row->getCacheWriteRate()),
            ]),
            PriceUnit::Per1mTokensUnified => self::perMillion((string) $tokens->total(), $row->getAnyRate()),
        };

        $cost = $offPeak !== null && $offPeak->applies($minuteOfDayUtc)
            ? Decimal::mul($base, $offPeak->multiplier())
            : $base;

        return Decimal::format6(Decimal::roundHalfUp($cost, 6));
    }

    /**
     * tokens × rate / 1 000 000 — точное десятичное произведение/деление.
     */
    private static function perMillion(string $tokens, null|string $rate): string
    {
        if ($rate === null) {
            return '0';
        }

        return Decimal::div(Decimal::mul($tokens, $rate), self::TOKEN_DENOMINATOR);
    }
}
