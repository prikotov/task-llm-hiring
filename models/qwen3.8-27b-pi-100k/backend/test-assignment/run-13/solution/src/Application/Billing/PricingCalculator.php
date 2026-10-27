<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money;
use App\Domain\PriceUnit;
use App\Infrastructure\Provider;
use App\Infrastructure\PriceRow;
use App\Infrastructure\UsageEvent;
use InvalidArgumentException;

/**
 * Ядро тарификации: базовая стоимость по схеме, оф-пик,
 * единственное округление HALF_UP до 6 знаков.
 */
final readonly class PricingCalculator
{
    /**
     * Базовая стоимость события (без оф-пика, безокруглённая).
     */
    public function baseCost(PriceRow $row, UsageEvent $event): string
    {
        return PriceUnit::cost(
            $row->unitValue(),
            $row->ratesValue(),
            $event->tokensInputValue(),
            $event->tokensOutputValue(),
            $event->tokensCacheReadValue(),
            $event->tokensCacheWriteValue(),
        );
    }

    /**
     * Итоговая стоимость события:
     * 1) базовая стоимость по схеме;
     * 2) умножение на off_peak.multiplier, если время суток попадает в окно;
     * 3) округление HALF_UP до 6 знаков (единственное в цепочке).
     */
    public function eventCost(
        UsageEvent $event,
        PriceRow $row,
        ?Provider $provider = null,
    ): RateResult {
        $raw = $this->baseCost($row, $event);
        $applied = $provider !== null ? $this->applyOffPeak($raw, $event->tsValue(), $provider) : $raw;

        return new RateResult(
            rawCost: $raw,
            appliedCost: $applied,
            roundedCost: Money::round($applied, 6),
        );
    }

    /**
     * Применить оф-пик, если настроен и время попадает в окно.
     */
    public function applyOffPeak(string $cost, \DateTimeImmutable $ts, ?Provider $provider): string
    {
        if ($provider === null) {
            return $cost;
        }
        $multiplier = $provider->offPeakMultiplierValue();
        $start = $provider->offPeakWindowStartValue();
        $end = $provider->offPeakWindowEndValue();

        if ($multiplier === null || $start === null || $end === null) {
            return $cost;
        }

        if (! $this->inWindow($ts, $start, $end)) {
            return $cost;
        }

        return Money::mul($cost, $multiplier);
    }

    /**
     * Время суток входит в окно [startHM, endHM), окно может пересекать полночь.
     */
    public function inWindow(\DateTimeImmutable $ts, string $startHM, string $endHM): bool
    {
        $t = ((int) $ts->format('H')) * 60 + ((int) $ts->format('i'));
        $start = self::hmToMinutes($startHM);
        $end = self::hmToMinutes($endHM);

        if ($start === $end) {
            return false;
        }

        if ($start < $end) {
            return $t >= $start && $t < $end;
        }

        // Окно пересекает полночь.
        return $t >= $start || $t < $end;
    }

    private static function hmToMinutes(string $hm): int
    {
        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $hm, $m) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректное время: "%s"', $hm));
        }

        return ((int) $m[1]) * 60 + ((int) $m[2]);
    }
}

final readonly class RateResult
{
    public function __construct(
        public string $rawCost,
        public string $appliedCost,
        public string $roundedCost,
    ) {
    }
}
