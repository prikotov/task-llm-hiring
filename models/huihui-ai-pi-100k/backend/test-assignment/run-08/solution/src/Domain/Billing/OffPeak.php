<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use DateTimeImmutable;

/**
 * Оф-пиковое окно скидки провайдера (время суток UTC).
 *
 * Окно [window_start, window_end), включ/искл; возможно переход через полночь.
 * Окно, для которого start == end, трактуется как целые сутки (см. допущения
 * в PR_DESCRIPTION.md).
 *
 * @readonly
 */
final class OffPeak
{
    public function __construct(
        public readonly string $windowStart, // "HH:MM"
        public readonly string $windowEnd,   // "HH:MM"
        public readonly string $multiplier,  // строка-число, напр. "0.5"
    ) {
    }

    public function appliesAt(DateTimeImmutable $ts): bool
    {
        $mins = (int) $ts->format('G') * 60 + (int) $ts->format('i');
        $start = self::windowToMinutes($this->windowStart);
        $end = self::windowToMinutes($this->windowEnd);

        if ($start === $end) {
            return true;
        }

        return $start < $end
            ? $mins >= $start && $mins < $end
            : $mins >= $start || $mins < $end; // окно через полночь
    }

    private static function windowToMinutes(string $window): int
    {
        return (int) substr($window, 0, 2) * 60 + (int) substr($window, 3, 2);
    }
}
