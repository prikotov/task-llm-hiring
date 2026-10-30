<?php

declare(strict_types=1);

namespace App\Billing\Pricing;

use App\Shared\Math\Decimal;
use InvalidArgumentException;

/**
 * Расчёт стоимости одного события LLM-запроса.
 *
 * Правила (TASK.md «Правила тарификации»):
 *  - per_1m_tokens:      cost = Σ tokens_вид × rate_вид / 1 000 000;
 *  - per_1m_tokens_unified: cost = (сумма всех токенов) × rate_any / 1 000 000;
 *  - оф-пик: если время события (UTC) в окне провайдера — стоимость ВЕСЬХ
 *    события умножается на multiplier (до единственного округления);
 *  - все промежуточные значения — точные десятичные (bcmath, без округления),
 *    единственное округление — итог события, 6 знаков, HALF_UP.
 */
final class PricingCalculator
{
    /** Виды токенов в схеме per_1m_tokens. */
    public const KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    /**
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     * @param array<string, string> $rates цены по видам (десятичные строки)
     * @param string|null $offPeakMultiplier null — событие вне окна оф-пика
     *
     * @return string итоговая стоимость в USD, ровно 6 знаков после точки
     */
    public function calculateEventCost(
        PriceUnit $unit,
        array $tokens,
        array $rates,
        ?string $offPeakMultiplier,
    ): string {
        $cost = $this->baseCost($unit, $tokens, $rates);

        if ($offPeakMultiplier !== null) {
            $cost = Decimal::mul($cost, $offPeakMultiplier);
        }

        return Decimal::roundHalfUp6($cost);
    }

    /**
     * Точная (неокруглённая) базовая стоимость по токенам и ценам.
     *
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     * @param array<string, string> $rates
     */
    private function baseCost(PriceUnit $unit, array $tokens, array $rates): string
    {
        if ($unit === PriceUnit::Per1mTokensUnified) {
            $totalTokens = 0;
            foreach (self::KINDS as $kind) {
                $totalTokens += (int) ($tokens[$kind] ?? 0);
            }

            return $this->tokenTerm($totalTokens, $rates['any'] ?? throw new InvalidArgumentException(
                'Схема per_1m_tokens_unified требует цену вида «any»',
            ));
        }

        $sum = '0';
        foreach (self::KINDS as $kind) {
            $sum = Decimal::add(
                $sum,
                $this->tokenTerm(
                    (int) ($tokens[$kind] ?? 0),
                    $rates[$kind] ?? throw new InvalidArgumentException(
                        sprintf('Схема per_1m_tokens требует цену вида «%s»', $kind),
                    ),
                ),
            );
        }

        return $sum;
    }

    /** tokens × rate / 1 000 000 — точно, без округления (деление на 10^6 сдвигает запятую). */
    private function tokenTerm(int $tokens, string $rate): string
    {
        if ($tokens === 0) {
            return '0';
        }

        return Decimal::div(Decimal::mul((string) $tokens, $rate), '1000000');
    }
}
