<?php

declare(strict_types=1);

namespace App\Billing\OffPeak;

use App\Support\TimeOfDay;

/**
 * Оф-пиковая скидка: решает, действует ли на событие окно провайдера.
 * Окно задаётся на уровне провайдера (window_start включительно,
 * window_end исключительно, время суток UTC) и действует для всех его моделей.
 */
final class OffPeakPricing
{
    /**
     * @return string|null множитель, если событие попадает в окно; null — скидка не применяется
     */
    public static function multiplierAt(
        ?string $windowStart,
        ?string $windowEnd,
        ?string $multiplier,
        int $minutesOfDay,
    ): ?string {
        if ($windowStart === null || $windowEnd === null || $multiplier === null) {
            return null;
        }

        $window = new OffPeakWindow(
            TimeOfDay::parseHhMm($windowStart),
            TimeOfDay::parseHhMm($windowEnd),
        );

        return $window->contains($minutesOfDay) ? $multiplier : null;
    }
}
