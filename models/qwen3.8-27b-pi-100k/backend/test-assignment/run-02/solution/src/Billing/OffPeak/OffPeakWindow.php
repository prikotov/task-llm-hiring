<?php

declare(strict_types=1);

namespace App\Billing\OffPeak;

use InvalidArgumentException;

/**
 * Окно оф-пика провайдера по времени суток (UTC), в минутах от полуночи.
 *
 * Границы: [startMinutes, endMinutes) — начало включительно,
 * конец исключительный. Окно может переходить через полночь
 * (например, 16:30–00:30). Если startMinutes === endMinutes — окно
 * пустое: скидка не применяется вообще (допущение зафиксировано в
 * PR_DESCRIPTION.md).
 */
final class OffPeakWindow
{
    public function __construct(
        public readonly int $startMinutes,
        public readonly int $endMinutes,
    ) {
        if ($startMinutes < 0 || $startMinutes > 1439 || $endMinutes < 0 || $endMinutes > 1439) {
            throw new InvalidArgumentException('Window boundaries must be in 0..1439 minutes');
        }
    }

    public function contains(int $minutesOfDay): bool
    {
        if ($minutesOfDay < 0 || $minutesOfDay > 1439) {
            throw new InvalidArgumentException('minutesOfDay must be in 0..1439');
        }

        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }

        if ($this->startMinutes < $this->endMinutes) {
            return $minutesOfDay >= $this->startMinutes
                && $minutesOfDay < $this->endMinutes;
        }

        // Окно пересекает полночь: [start, 24:00) ∪ [00:00, end)
        return $minutesOfDay >= $this->startMinutes
            || $minutesOfDay < $this->endMinutes;
    }
}
