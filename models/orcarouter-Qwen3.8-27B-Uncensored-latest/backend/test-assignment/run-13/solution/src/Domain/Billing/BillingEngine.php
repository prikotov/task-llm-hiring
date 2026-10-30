<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Money\DecimalMoney;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceBook;
use App\Domain\Pricing\PriceRow;
use App\Domain\Usage\TokenUsage;

/**
 * Pure billing rules (no I/O):
 *  - resolve the price row in force at the event timestamp (most recent validFrom ≤ ts);
 *  - no row ⇒ the event is unrated, not an error;
 *  - compute the exact (unrounded) cost from the row's decimal rates
 *    (tokens × rate ÷ 1 000 000 per "per_1m_tokens" unit);
 *  - apply the provider off-peak multiplier to the total event cost when the
 *    event's UTC time-of-day falls in the window;
 *  - single, final rounding to 6 digits (HALF_UP, 2.5 → 3).
 *
 * No floating point anywhere in this path.
 */
final class BillingEngine
{
    /** "per_1m_tokens" ⇒ rate is per 1 000 000 tokens. */
    public const TOKENS_PER_UNIT = 1000000;

    public function __construct(
        private readonly PriceBook $book,
    ) {
    }

    public function rateEvent(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $tsUtc,
        TokenUsage $tokens,
    ): RatingResult {
        $row = $this->book->rateInForce($providerCode, $modelCode, $tsUtc);
        if ($row === null) {
            return RatingResult::unrated($providerCode, $modelCode);
        }

        $exact = $this->exactCost($row, $tokens);

        $offPeak = $this->book->providerOffPeak($providerCode);
        $offPeakApplied = false;
        if ($offPeak !== null && $offPeak->inWindow($this->utcMinutesOfDay($tsUtc))) {
            $exact = $exact->multiply(DecimalMoney::fromDecimalString($offPeak->multiplier));
            $offPeakApplied = true;
        }

        $cost = $exact->roundTo(DecimalMoney::ROUND_SCALE);

        return RatingResult::rated(
            providerCode: $providerCode,
            modelCode: $modelCode,
            priceRow: $row,
            offPeak: $offPeakApplied ? $offPeak : null,
            offPeakApplied: $offPeakApplied,
            exactCost: $exact,
            cost: $cost,
        );
    }

    private function exactCost(PriceRow $row, TokenUsage $t): DecimalMoney
    {
        if ($row->isUnified()) {
            return DecimalMoney::fromDecimalString((string) $t->total())
                ->multiply(DecimalMoney::fromDecimalString($row->rate('any')))
                ->divideByInteger(self::TOKENS_PER_UNIT);
        }

        return DecimalMoney::zero()
            ->add($this->term($t->input, $row->rate('input')))
            ->add($this->term($t->output, $row->rate('output')))
            ->add($this->term($t->cacheRead, $row->rate('cache_read')))
            ->add($this->term($t->cacheWrite, $row->rate('cache_write')));
    }

    private function term(int $tokens, string $rate): DecimalMoney
    {
        if ($tokens === 0) {
            return DecimalMoney::zero();
        }

        return DecimalMoney::fromDecimalString((string) $tokens)
            ->multiply(DecimalMoney::fromDecimalString($rate))
            ->divideByInteger(self::TOKENS_PER_UNIT);
    }

    private function utcMinutesOfDay(\DateTimeImmutable $tsUtc): int
    {
        $utc = $tsUtc->setTimezone(new \DateTimeZone('UTC'));
        return (int) $utc->format('G') * 60 + (int) $utc->format('i');
    }
}
