<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Usage\TokenBreakdown;

/**
 * Тарификатор: точный расчёт стоимости события до единственного
 * допущенного округления.
 *
 * Порядок: базовая стоимость по схеме → (по возможности) оф-пиковый
 * множитель → HALF_UP до 6 знаков ровно один раз.
 */
final readonly class RateCalculator
{
    public function __construct(
        private ?OffPeakWindow $offPeak,
        private PricedModel $pricedModel,
    ) {
    }

    /**
     * Точная (неокруглённая) стоимость события.
     */
    public function exactCost(TokenBreakdown $tokens, string $hhmmUtc): Money
    {
        $cost = $this->pricedModel->costFor($tokens);

        if ($this->offPeak !== null && $this->offPeak->contains($hhmmUtc)) {
            $cost = $cost->multiplyBy($this->offPeak->multiplier);
        }

        return $cost;
    }

            /**
     * Итоговая стоимость события: единственное допустимое округление —
     * до 6 знаков HALF_UP.
     */
    public function price(TokenBreakdown $tokens, string $hhmmUtc): Money
    {
        return $this->exactCost($tokens, $hhmmUtc)->roundTo6();
    }
}
