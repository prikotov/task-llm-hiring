<?php

declare(strict_types=1);

namespace App\Domain\Billing\Pricing;

use App\Domain\Billing\Entity\PriceRow;
use App\Domain\Billing\Entity\UsageEvent;
use App\Domain\Billing\Enum\PriceUnit;
use App\Domain\Billing\Util\Money;

/**
 * Расчёт стоимости события по строке цен и оф-пик-скidке.
 *
 * Порядок: базовая стоимость по схеме тарификации (без округления) →
 * умножение на off-peak-множитель (если ts в окне) → единственное
 * округление итога события до 6 знаков (HALF_UP).
 */
final class BillingCalculator
{
    /**
     * @return string итоговая стоимость (6 знаков после точки)
     */
    public function rate(UsageEvent $event, PriceRow $row, ?OffPeak $offPeak): string
    {
        $base = match ($row->unitEnum()) {
            PriceUnit::Per1mTokens => Money::perTokenCost(
                $event->tokensInput,
                $event->tokensOutput,
                $event->tokensCacheRead,
                $event->tokensCacheWrite,
                $row->rateInput ?? '0',
                $row->rateOutput ?? '0',
                $row->rateCacheRead ?? '0',
                $row->rateCacheWrite ?? '0'
            ),
            PriceUnit::Per1mTokensUnified => Money::unifiedCost(
                $event->tokensInput + $event->tokensOutput
                + $event->tokensCacheRead + $event->tokensCacheWrite,
                $row->rateAny ?? '0'
            ),
        };

        if ($offPeak !== null && $offPeak->isInWindow($event->ts)) {
            $base = Money::mul($base, $offPeak->multiplier);
        }

        return Money::roundHalfUp($base);
    }

    /**
     * Базовая стоимость (без off-peak, без округления) — для повторного
     * расчёта событий при reprice. В реальных сценариях используется только
     * через rate(); приведён в отдельный метод для читаемости.
     */
    public function baseCost(UsageEvent $event, PriceRow $row): string
    {
        return match ($row->unitEnum()) {
            PriceUnit::Per1mTokens => Money::perTokenCost(
                $event->tokensInput,
                $event->tokensOutput,
                $event->tokensCacheRead,
                $event->tokensCacheWrite,
                $row->rateInput ?? '0',
                $row->rateOutput ?? '0',
                $row->rateCacheRead ?? '0',
                $row->rateCacheWrite ?? '0'
            ),
            PriceUnit::Per1mTokensUnified => Money::unifiedCost(
                $event->tokensInput + $event->tokensOutput
                + $event->tokensCacheRead + $event->tokensCacheWrite,
                $row->rateAny ?? '0'
            ),
        };
    }
}
