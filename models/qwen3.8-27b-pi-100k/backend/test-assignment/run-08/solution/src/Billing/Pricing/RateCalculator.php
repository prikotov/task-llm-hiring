<?php

declare(strict_types=1);

namespace App\Billing\Pricing;

use App\Billing\ValueObject\Decimal;
use App\Billing\ValueObject\Price;
use App\Billing\ValueObject\TokenUsage;

/**
 * Чистая арифметика тарификации (без БД и без округления результата):
 *
 *  per_1m_tokens:         cost = Σ tokens_вид × rate_вид / 1 000 000
 *  per_1m_tokens_unified: cost = (Σ все токены) × rate_any / 1 000 000
 *
 * Оф-пиковый множитель — отдельная операция умножения поверх суммы события.
 * Округление (единственное, HALF_UP до 6 знаков) прилагается вызывающим
 * доменным сервисом — см. RateEngine.
 */
final class RateCalculator
{
    private const DIVISOR = '1000000';

    public function cost(Price $price, TokenUsage $usage, ?Decimal $offPeakMultiplier): Decimal
    {
        $base = match ($price->unit) {
            \App\Billing\Enum\PricingUnit::Per1MTokens => $this->sumPerKind($price, $usage),
            \App\Billing\Enum\PricingUnit::Per1MTokensUnified => $this->unified($price, $usage),
        };

        return $offPeakMultiplier === null ? $base : $base->mul($offPeakMultiplier);
    }

    private function sumPerKind(Price $price, TokenUsage $usage): Decimal
    {
        return Decimal::zero()
            ->add($this->amount($usage->input, $price->rate('input')))
            ->add($this->amount($usage->output, $price->rate('output')))
            ->add($this->amount($usage->cacheRead, $price->rate('cache_read')))
            ->add($this->amount($usage->cacheWrite, $price->rate('cache_write')));
    }

    private function unified(Price $price, TokenUsage $usage): Decimal
    {
        return $this->amount($usage->total(), $price->rate('any'));
    }

    private function amount(int $tokens, Decimal $rate): Decimal
    {
        return Decimal::of($tokens)->mul($rate)->div(Decimal::of(self::DIVISOR));
    }
}
