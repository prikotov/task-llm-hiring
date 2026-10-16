<?php

declare(strict_types=1);

namespace App\Domain;

use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use function bcadd;

/**
 * Расчёт стоимости события по строке цен.
 *
 * Правила (TASK.md):
 *  - per_1m_tokens:        cost = Σ tokens_вид × rate_вид / 1 000 000
 *  - per_1m_tokens_unified: cost = Σ всех токенов × rate_any / 1 000 000
 *  - оф-пик провайдера:    вся стоимость события × multiplier
 *  - единственное округление — итоговой стоимости события до 6 знаков HALF_UP.
 */
final class Rater
{
    /**
     * @return string итоговая стоимость события, ровно 6 знаков после точки
     */
    public function rate(UsageEvent $event, PriceRow $price): string
    {
        $rawCost = $this->rawCost($event, $price);

        $offPeak = new OffPeak(
            $event->provider->offPeakWindowStart,
            $event->provider->offPeakWindowEnd,
            $event->provider->offPeakMultiplier,
        );
        if ($offPeak->contains(OffPeak::minutesOf($event->occurredAt))) {
            $rawCost = Decimal::mul($rawCost, (string) $offPeak->multiplier());
        }

        return Decimal::roundHalfUp6($rawCost);
    }

    private function rawCost(UsageEvent $event, PriceRow $price): string
    {
        $tokens = [
            'input' => $event->tokensInput,
            'output' => $event->tokensOutput,
            'cache_read' => $event->tokensCacheRead,
            'cache_write' => $event->tokensCacheWrite,
        ];

        if ($price->unit === PriceRow::UNIT_UNIFIED) {
            $total = array_sum($tokens);

            return Decimal::div(Decimal::mul((string) $total, self::rateOf($price, PriceRow::RATE_UNIFIED)), '1000000');
        }

        $cost = '0';
        foreach (PriceRow::RATES_PER_TOKEN as $kind) {
            if ($tokens[$kind] <= 0) {
                continue;
            }
            $cost = bcadd($cost, Decimal::mul((string) $tokens[$kind], self::rateOf($price, $kind)), Decimal::CALC_SCALE);
        }

        /* делим на 1 000 000 без округления; единственное округление — в rate() */
        return Decimal::div($cost, '1000000');
    }

    private static function rateOf(PriceRow $price, string $kind): string
    {
        $rate = $price->rates[$kind] ?? null;
        if ($rate === null) {
            throw new DomainException(
                sprintf('Строка цены %d (схема %s) не содержит сумму "%s"', $price->id, $price->unit, $kind)
            );
        }

        return (string) $rate;
    }
}
