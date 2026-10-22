<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Decimal;
use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use App\Domain\Unit;
use App\Entity\PriceRow;

/**
 * Точная (без конечного округления) стоимость события по строке цен
 * со схемой тарификации и, при наличии, с множителем оф-пика провайдера.
 *
 * Все промежуточные вычисления — bcmath с резервом точности (Decimal),
 * float не используется.
 */
final class CostCalculator
{
    private const ONE_MILLION = '1000000';

    /**
     * cost   = Σ(tokens_вид × rate_вид) / 1 000 000   — per_1m_tokens;
     * cost   = (Σ all tokens) × rate_any / 1 000 000  — per_1m_tokens_unified;
     * итог   = cost × off_peak.multiplier                     — если время суток
     *          ts (UTC) попадает в окно [window_start, window_end) провайдера.
     *
     * Возврат — значение перед окончательным HALF_UP-округлением; оно применяется
     * единожды на уровне события (см. RateService / RepriceService).
     *
     * @throws \DomainException
     */
    public function priceCost(
        PriceRow $row,
        TokenUsage $usage,
        \DateTimeImmutable $eventTs,
        ?OffPeakWindow $offPeak,
    ): Decimal {
        $base = match ($row->getUnit()) {
            Unit::PerToken => $this->perToken($row, $usage),
            Unit::PerTokenUnified => Decimal::of($usage->total())
                ->mul($row->rateFor('any'))
                ->div(self::ONE_MILLION),
        };

        if ($offPeak !== null && $offPeak->contains($eventTs)) {
            $base = $base->mul($offPeak->multiplier());
        }

        return $base;
    }

    private function perToken(PriceRow $row, TokenUsage $usage): Decimal
    {
        return Decimal::zero()
            ->add(Decimal::of($usage->input)->mul($row->rateFor('input'))->div(self::ONE_MILLION))
            ->add(Decimal::of($usage->output)->mul($row->rateFor('output'))->div(self::ONE_MILLION))
            ->add(Decimal::of($usage->cacheRead)->mul($row->rateFor('cache_read'))->div(self::ONE_MILLION))
            ->add(Decimal::of($usage->cacheWrite)->mul($row->rateFor('cache_write'))->div(self::ONE_MILLION));
    }
}
