<?php

declare(strict_types=1);

namespace App\Domain;

use App\Support\Money;

/**
 * Расчёт стоимости одного события по строке цен (и оф-пикой провайдера).
 *
 * Все промежуточные вычисления — без округления (bcmath); единственное
 * округление — итоговая стоимость события до 6 знаков, HALF_UP.
 */
final class CostCalculator
{
    public static function calculate(PriceRow $row, UsageEvent $event): string
    {
        $rates = $row->getRates();

        if ($row->getUnit() === BillingUnit::PerToken) {
            $cost = Money::add(
                self::term($event->getTokensInput(), $rates['input']),
                self::term($event->getTokensOutput(), $rates['output']),
                self::term($event->getTokensCacheRead(), $rates['cache_read']),
                self::term($event->getTokensCacheWrite(), $rates['cache_write']),
            );
        } else {
            $total = $event->getTokensInput()
                + $event->getTokensOutput()
                + $event->getTokensCacheRead()
                + $event->getTokensCacheWrite();
            $cost = self::term($total, $rates['any']);
        }

        $provider = $row->getProvider();
        if ($provider->isInOffPeak($event->getTs()) && $provider->getOffPeakMultiplier() !== null) {
            $cost = Money::mul($cost, $provider->getOffPeakMultiplier());
        }

        return Money::roundHalfUp($cost, 6);
    }

    /** price per 1 000 000 tokens → cost = tokens * rate / 1_000_000 (без округления). */
    private static function term(int $tokens, string $rate): string
    {
        return Money::div(Money::mul((string) $tokens, $rate), '1000000');
    }
}
