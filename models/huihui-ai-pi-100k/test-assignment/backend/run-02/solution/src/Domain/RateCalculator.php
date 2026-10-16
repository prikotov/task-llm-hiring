<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Ядро биллинга: стоимость события из токенов, строки цен и оф-пиковой скидки.
 *
 * Правила (согласно TASK.md):
 *  - per_1m_tokens:            cost = Σ tokens_вид × rate_вид / 1 000 000;
 *  - per_1m_tokens_unified:    cost = Σ_все_виды tokens × rate_any / 1 000 000;
 *  - если время суток ts (UTC) в оф-пиковом окне провайдера [start; end),
 *    стоимость события умножается на multiplier;
 *  - все промежуточные вычисления — точная десятичная арифметика (bcmath,
 *    масштаб 16), float не используется;
 *  - ровно одно округление: итоговая стоимость события до 6 знаков, HALF_UP;
 *  - суммы отчёта — сумма уже округлённых стоимостей, без повторного округления.
 */
final class RateCalculator
{
    /** Делитель схемы «цена за 1 000 000 токенов». */
    private const TOKENS_PER_UNIT = '1000000';

    /** Ключ единой ставки при схеме per_1m_tokens_unified. */
    private const KIND_UNIFIED = 'any';

    /**
     * Итоговая стоимость события (уже округлена до 6 знаков, HALF_UP).
     *
     * @param array<string, string> $rates ставки по видам токенов, десятичные строки;
     *                                     набор ключей соответствует $unit
     */
    public static function eventCost(
        TokenUsage $usage,
        PricingUnit $unit,
        array $rates,
        ?OffPeakWindow $offPeak,
        \DateTimeInterface $utcTimestamp,
    ): Money {
        $cost = self::baseCost($usage, $unit, $rates);

        if ($offPeak !== null && $offPeak->covers($utcTimestamp)) {
            $cost = $cost->times($offPeak->multiplier());
        }

        return $cost->roundHalfUp(Money::US_DECIMALS);
    }

    /**
     * Базовая стоимость без скидки, точная десятичная арифметика.
     *
     * @param array<string, string> $rates
     */
    private static function baseCost(TokenUsage $usage, PricingUnit $unit, array $rates): Money
    {
        if ($unit === PricingUnit::PerMillionUnified) {
            return self::perMillionTokens($usage->total(), $rates[self::KIND_UNIFIED]);
        }

        $cost = Money::zero();
        foreach ($usage->byKind() as $kind => $tokens) {
            if ($tokens > 0) {
                $cost = $cost->plus(self::perMillionTokens($tokens, $rates[$kind]));
            }
        }

        return $cost;
    }

    /** cost = tokens × rate / 1 000 000 (без округления, точная арифметика). */
    private static function perMillionTokens(int $tokens, string $rate): Money
    {
        return Money::fromDecimalString((string) $tokens)
            ->times($rate)
            ->dividedBy(self::TOKENS_PER_UNIT);
    }
}
