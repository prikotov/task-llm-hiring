<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Billing\Cost;


/**
 * Настройки оф-пиковой скидки провайдера.
 *
 * Окно времени суток UTC: [windowStart, windowEnd), включительно/исключительно.
 * Окно может переходить через полночь, например 16:30–00:30.
 * Время ровно `windowEnd` (в том числе 00:00 при переходе) вне окна.
 */
final class OffPeakPolicy
{
    /** @var int минуты от начала суток UTC */
    private int $start;

    /** @var int минуты от начала суток UTC */
    private int $end;

    public function __construct(
        public readonly string $windowStart,
        public readonly string $windowEnd,
        public readonly Cost $multiplier,
    ) {
        $this->start = self::minutesOfDay($windowStart);
        $this->end = self::minutesOfDay($windowEnd);
    }

    public function appliesAt(\DateTimeImmutable $momentUtc): bool
    {
        $minutes = (int) $momentUtc->format('G') * 60 + (int) $momentUtc->format('i');

        if ($this->start < $this->end) {
            return $minutes >= $this->start && $minutes < $this->end;
        }

        // Окно пересекает полночь: [start, 24:00) ∪ [00:00, end).
        return $minutes >= $this->start || $minutes < $this->end;
    }

    private static function minutesOfDay(string $hhmm): int
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hhmm, $m)) {
            throw new \InvalidArgumentException("Invalid time of day: {$hhmm}");
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }
}
