<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Money\Decimal;
use App\Domain\Pricing\Model;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\Provider;
use App\Domain\Usage\TokenUsage;

/**
 * Чистый расчёт стоимости события. Никакого I/O и никакого float:
 * все промежуточные величины — Decimal с большим масштабом,
 * округление одно — итоговой стоимости события до 6 знаков HALF_UP.
 */
final class CostCalculator
{
    public const COST_SCALE = 6;

    /**
     * @return Decimal|null стоимость события, округлённая до 6 знаков,
     *                      либо null, если в прайс-листе нет подходящей строки
     */
    public function calculate(
        Provider $provider,
        Model $model,
        iterable $priceRows,
        \DateTimeImmutable $occurredAt,
        TokenUsage $tokens,
    ): ?Decimal {
        $row = $model->priceAt($priceRows, $occurredAt);
        if (null === $row) {
            return null;
        }

        return $this->costOfRow($provider, $model, $row, $occurredAt, $tokens);
    }

    public function costOfRow(
        Provider $provider,
        Model $model,
        PriceRow $row,
        \DateTimeImmutable $occurredAt,
        TokenUsage $tokens,
    ): Decimal {
        $cost = $model->costOf($row, $tokens);

        $offPeak = $provider->offPeak;
        if (null !== $offPeak && $offPeak->contains($occurredAt)) {
            $cost = $cost->mul($offPeak->multiplier);
        }

        return $cost->roundHalfUp(self::COST_SCALE);
    }
}
