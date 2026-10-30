<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Money\Decimal;

/**
 * Ядро тарификации: чистая предметная логика расчёта стоимости события.
 *
 * Отдельным методом реализованы правила 1-4 из Taks.md:
 *   - выбор схемы (per_1m_tokens / per_1m_tokens_unified);
 *   - десятичная арифметика без промежуточного округления;
 *   - оф-пик (множитель всего события);
 *   - единственное округление — итоговой стоимости события (HALF_UP, 6 знаков).
 *
 * Класс не зависит от СУБД/фреймворка — его удобно покрывать тестами.
 */
final class CostCalculator
{
    public function rate(
        TokenCounts $tokens,
        RateUnit $unit,
        array $rates,
        ?OffPeak $offPeak,
        int $minuteOfDayUtc,
    ): string {
        $cost = $this->baseCostByScheme($tokens, $unit, $rates);

        if ($offPeak !== null && $offPeak->appliesAt($minuteOfDayUtc)) {
            $cost = Decimal::mul($cost, $offPeak->multiplier);
        }

        // Единственное округление: итоговая стоимость события до 6 знаков.
        return Decimal::roundHalfUp($cost, 6);
    }

    private function baseCostByScheme(TokenCounts $tokens, RateUnit $unit, array $rates): string
    {
        return match ($unit) {
            RateUnit::Per1mTokens => Decimal::perMillion($this->perKindSum($tokens, $rates)),
            RateUnit::Per1mTokensUnified => Decimal::perMillion(
                Decimal::mul($tokens->total(), (string) ($rates['any'] ?? '0'))
            ),
        };
    }

    private function perKindSum(TokenCounts $tokens, array $rates): string
    {
        $sum = '0';
        foreach (TokenCounts::KINDS as $kind) {
            $sum = Decimal::add($sum, Decimal::mul($tokens->kind($kind), (string) ($rates[$kind] ?? '0')));
        }

        return $sum;
    }
}
