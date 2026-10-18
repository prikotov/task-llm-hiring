<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Catalog\OffPeak;
use App\Domain\Catalog\PricingUnit;

/**
 * Расчёт стоимости одного события использования.
 *
 * Порядок вычислений закреплён:
 *   1) точный расчёт по схеме тарификации (сумма по видам токенов,
 *      без округления; бcmath с масштабом см. DecimalMath::SCALE);
 *   2) если время суток запроса (UTC) в окне оф-пика провайдера —
 *      умножение на multiplier (тоже без округления);
 *   3) единственное округление — HALF_UP до 6 знаков после точки.
 */
final class CostCalculator
{
    public function calculate(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        PricingUnit $unit,
        array $rates,
        ?OffPeak $offPeak,
        \DateTimeImmutable $tsUtc,
    ): string {
        $amount = match ($unit) {
            PricingUnit::Per1mTokens => bcadd(
                bcadd(
                    DecimalMath::tokenCost($tokensInput, (string) $rates['input']),
                    DecimalMath::tokenCost($tokensOutput, (string) $rates['output']),
                    DecimalMath::SCALE,
                ),
                bcadd(
                    DecimalMath::tokenCost($tokensCacheRead, (string) $rates['cache_read']),
                    DecimalMath::tokenCost($tokensCacheWrite, (string) $rates['cache_write']),
                    DecimalMath::SCALE,
                ),
                DecimalMath::SCALE,
            ),
            PricingUnit::Per1mTokensUnified => DecimalMath::tokenCost(
                $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite,
                (string) $rates['any'],
            ),
        };

        if ($offPeak !== null && $offPeak->applies($tsUtc)) {
            $amount = bcmul($amount, $offPeak->multiplier(), DecimalMath::SCALE);
        }

        return DecimalMath::roundHalfUp($amount, DecimalMath::COST_SCALE);
    }
}
