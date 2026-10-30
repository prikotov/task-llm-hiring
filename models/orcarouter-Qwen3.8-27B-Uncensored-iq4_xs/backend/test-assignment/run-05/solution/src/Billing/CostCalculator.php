<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\RateUnit;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\OffPeak;
use App\Domain\ValueObject\TokenUsage;

/**
 * Точная десятичная оценка стоимости события (bcmath, масштаб с запасом).
 *
 * Порядок: базовая цена по схеме тарификации → off-peak-множитель →
 * единственное округление итога до 6 знаков (HALF_UP).
 */
final class CostCalculator
{
    public function compute(
        PriceRow $price,
        TokenUsage $tokens,
        ?OffPeak $offPeak,
        \DateTimeImmutable $tsUtc,
    ): Money {
        $base = $this->baseCost($price, $tokens);

        if ($offPeak !== null && $offPeak->covers($tsUtc)) {
            $base = $base->multipliedBy($offPeak->multiplier());
        }

        return $base->roundTo6();
    }

    private function baseCost(PriceRow $price, TokenUsage $tokens): Money
    {
        if ($price->unit() === RateUnit::Per1mTokensUnified) {
            $totalNumerator = bcmul((string) $tokens->total(), $price->rate('any'), 6);

            return Money::of($totalNumerator)->dividedBy(PriceRow::TOKENS_PER_UNIT);
        }

        // per_1m_tokens: Σ tokens_вид × rate_вид / 1 000 000
        $totalNumerator = '0';
        foreach (TokenUsage::KINDS as $kind) {
            $term = bcmul((string) $tokens->get($kind), $price->rate($kind), 6);
            $totalNumerator = bcadd($totalNumerator, $term, 6);
        }

        return Money::of($totalNumerator)->dividedBy(PriceRow::TOKENS_PER_UNIT);
    }
}
