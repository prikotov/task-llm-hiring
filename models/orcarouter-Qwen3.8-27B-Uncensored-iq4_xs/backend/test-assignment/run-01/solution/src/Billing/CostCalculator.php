<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;
use App\Util\MoneyCalculator;
use DateTimeImmutable;

/**
 * Расчёт стоимости события по строке цен.
 *
 * Порядок вычислений (без промежуточных округлений, bcmath-масштаб 20):
 *   1) сумма по видам токенов tokens × rate (или общая сумма токенов × rate_any);
 *   2) деление на 1 000 000 (цена задана за миллион токенов);
 *   3) умножение на множитель оф-пика, если ts попадает в окно;
 *   4) единственное округление: итоговая стоимость события — 6 знаков, HALF_UP.
 */
final class CostCalculator
{
    public static function computeEventCost(
        array $tokens,
        PriceRow $priceRow,
        ?OffPeakWindow $offPeak,
        DateTimeImmutable $ts,
    ): string {
        $rates = $priceRow->getRates();

        if ($priceRow->getUnit() === PriceRow::UNIT_PER_TOKEN) {
            $gross = '0';
            foreach (PriceRow::TOKEN_KINDS as $kind) {
                $gross = MoneyCalculator::add(
                    $gross,
                    MoneyCalculator::multiply((string) $tokens[$kind], $rates[$kind]),
                );
            }
        } else {
            $totalTokens = 0;
            foreach (PriceRow::TOKEN_KINDS as $kind) {
                $totalTokens += $tokens[$kind];
            }
            $gross = MoneyCalculator::multiply((string) $totalTokens, $rates[PriceRow::UNIFIED_RATE_KEY]);
        }

        $cost = MoneyCalculator::divide($gross, (string) MoneyCalculator::UNITS_PER_RATE);

        if ($offPeak !== null && $offPeak->isInside($ts)) {
            $cost = MoneyCalculator::multiply($cost, $offPeak->multiplier());
        }

        return MoneyCalculator::roundHalfUp($cost);
    }
}
