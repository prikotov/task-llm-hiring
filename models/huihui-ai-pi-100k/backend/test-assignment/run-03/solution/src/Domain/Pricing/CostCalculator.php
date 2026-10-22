<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Common\Decimal;
use DateTimeInterface;

/**
 * Расчёт точной стоимости одного события.
 *
 * Правила (см. TASK.md, «Правила тарификации»):
 *  1) per_1m_tokens — сумма по видам: Σ tokens_вид × ставка_вид / 1 000 000;
 *  2) per_1m_tokens_unified — Σвсех токенов × ставка_any / 1 000 000;
 *  3) если время суток ts в оф-пиковом окне провайдера — итог × множитель;
 *  4) промежуточные величины — точная десятичная арифметика с запасом,
 *     единственное округление — итог события до 6 знаков HALF_UP.
 */
final class CostCalculator
{
    private const MILLION = '1000000';

    /** Возвращает стоимость события — точную десятичную строку с 6 знаками после точки. */
    public function eventCost(
        TokenUsage $usage,
        PriceRowValue $row,
        DateTimeInterface $at,
        ?OffPeakWindow $offPeak = null,
    ): string {
        $cost = $this->baseCost($usage, $row);

        if ($offPeak !== null && $offPeak->isWithin($at)) {
            $cost = $cost->mul(Decimal::of($offPeak->multiplier()));
        }

        return $cost->roundHalfUp(Decimal::MONEY_SCALE)->format(Decimal::MONEY_SCALE);
    }

    private function baseCost(TokenUsage $usage, PriceRowValue $row): Decimal
    {
        return match ($row->unit) {
            PriceUnit::Per1mTokens => $this->sumByKinds($usage, $row),
            PriceUnit::Per1mTokensUnified => $this->line(
                $usage->total(),
                $row->rates[TokenKind::Any->value] ?? '0',
            ),
        };
    }

    private function sumByKinds(TokenUsage $usage, PriceRowValue $row): Decimal
    {
        $sum = Decimal::zero();

        foreach (TokenKind::billable() as $kind) {
            $sum = $sum->add($this->line($usage->count($kind), $row->rates[$kind->value] ?? '0'));
        }

        return $sum;
    }

    /** tokens × ставка / 1 000 000 — точное десятичное значение с запасом точности. */
    private function line(int $tokens, string $rate): Decimal
    {
        if ($tokens === 0) {
            return Decimal::zero();
        }

        return Decimal::of($tokens)
            ->mul(Decimal::of($rate))
            ->div(Decimal::of(self::MILLION));
    }
}
