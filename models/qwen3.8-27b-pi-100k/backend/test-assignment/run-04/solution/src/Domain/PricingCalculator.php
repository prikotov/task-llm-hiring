<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Тарификация одного события:
 *
 *   base = Σ tokens_вид × rate_вид / 1 000 000      (per_1m_tokens)
 *   base = Σ tokens × rate_any / 1 000 000          (per_1m_tokens_unified)
 *   cost = base × off-peak-множитель (если ts в окне)
 *
 * Все промежуточные значения — точные bcmath (SCALE), единственное
 * округление — итог до 6 знаков HALF_UP.
 */
final class PricingCalculator
{
    public function rate(
        PriceSpec $price,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        ?OffPeak $offPeak,
        \DateTimeInterface $eventUtcTs,
    ): string {
        $cost = $price->baseCost($tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite);

        if ($offPeak !== null && $offPeak->appliesAt($eventUtcTs)) {
            $cost = \bcmul($cost, $offPeak->multiplier(), Money::SCALE);
        }

        return Money::toScale6($cost);
    }
}
