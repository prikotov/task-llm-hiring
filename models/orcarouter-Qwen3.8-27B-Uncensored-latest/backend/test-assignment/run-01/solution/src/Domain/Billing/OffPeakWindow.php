<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Catalog\Provider;

/**
 * Окно оф-пиковой скидки провайдера: [window_start, window_end) по UTC,
 * может переходить через полночь.
 *
 * Допущение: window_start == window_end трактуется как пустое окно
 * (скидка не применяется) — фиксированное решение неоговорённой детали.
 */
final class OffPeakWindow
{
    /**
     * @param string $start "HH:MM" (уже валидированный)
     * @param string $end   "HH:MM" (уже валидированный)
     */
    public function __construct(
        private readonly string $start,
        private readonly string $end,
    ) {}

    public static function fromProvider(Provider $provider): ?self
    {
        if ($provider->offPeakWindowStart === null
            || $provider->offPeakWindowEnd === null
            || $provider->offPeakMultiplier === null
        ) {
            return null;
        }

        return new self($provider->offPeakWindowStart, $provider->offPeakWindowEnd);
    }

    /** Момент времени в UTC (момент уже нормализован к UTC). */
    public function contains(\DateTimeImmutable $utcTime): bool
    {
        $time = $utcTime->format('H:i');

        if ($this->start === $this->end) {
            return false;
        }

        return $this->start < $this->end
            ? $time >= $this->start && $time < $this->end
            : $time >= $this->start || $time < $this->end;
    }
}
