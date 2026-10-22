<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Catalog\PriceLine;
use App\Domain\Catalog\Provider;
use App\Domain\Common\Money;
use App\Domain\Usage\UsageEvent;

use function bcadd;
use function bcdiv;
use function bcmul;

/**
 * Расчёт стоимости одного события.
 *
 * Промежуточные вычисления — без округления (bcmath, масштаб ≥ 16);
 * единственное округление — итог события до 6 знаков HALF_UP.
 */
final class RateCalculator
{
    private const PER_MILLION = '0.000001';

    public function eventCost(UsageEvent $event, PriceLine $line, Provider $provider): string
    {
        $cost = $this->rawCost($event, $line);

        $offPeak = $provider->offPeak();
        if ($offPeak !== null && $offPeak->isOffPeak($event->ts())) {
            $cost = bcmul($cost, $offPeak->multiplier(), Money::SCALE);
        }

        return Money::roundHalfUp($cost, Money::CENTS_PLACES);
    }

    /** Стоимость без скидки и без округления. */
    public function rawCost(UsageEvent $event, PriceLine $line): string
    {
        $tokens = $event->tokens();
        $rates = $line->rates();

        if ($line->scheme() === PriceScheme::PerTokenUnified) {
            $totalTokens = array_sum($tokens);

            $cost = bcmul((string) $totalTokens, $rates['any'], Money::SCALE);
        } else {
            $cost = '0';
            foreach ($tokens as $kind => $count) {
                if ($count === 0) {
                    continue;
                }

                $cost = bcadd($cost, bcmul((string) $count, $rates[$kind], Money::SCALE), Money::SCALE);
            }
        }

        // Деление на 1 000 000: 0.000001 — точное десятичное, результат не усекается.
        return bcdiv($cost, Money::MILLION, Money::SCALE);
    }
}
