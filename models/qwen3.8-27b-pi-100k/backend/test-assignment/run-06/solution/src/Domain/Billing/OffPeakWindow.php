<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use DateTimeImmutable;

/**
 * Оф-пиковое окно провайдера: [windowStart, windowEnd) по часам UTC дня,
 * может пересекать полночь (например 16:30–00:30).
 *
 * `multiplier` — десятичная строка (bcmath), никогда float.
 */
final class OffPeakWindow
{
    public function __construct(
        public readonly string $windowStart,
        public readonly string $windowEnd,
        public readonly string $multiplier,
    ) {
        self::assertHhMm($windowStart);
        self::assertHhMm($windowEnd);
        self::assertDecimal($multiplier);
    }

    /**
     * Попадает ли момент времени в окно [windowStart, windowEnd).
     */
    public function contains(DateTimeImmutable $momentUtc): bool
    {
        $t = (int) $momentUtc->format('G') * 60 + (int) $momentUtc->format('i');
        $start = self::toMinutes($this->windowStart);
        $end = self::toMinutes($this->windowEnd);

        if ($start <= $end) {
            return $t >= $start && $t < $end;
        }

        // Окно пересекает полночь: [start, 24:00) ∪ [00:00, end)
        return $t >= $start || $t < $end;
    }

    public static function toMinutes(string $hhMm): int
    {
        [$h, $m] = array_map('intdiv', explode(':', $hhMm, 2), [1, 1]);

        return $h * 60 + $m;
    }

    private static function assertHhMm(string $value): void
    {
        if (preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Off-peak boundary "%s" must be "HH:MM".', $value));
        }
    }

    private static function assertDecimal(string $value): void
    {
        if (preg_match('/^\d+(\.\d+)?$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Multiplier "%s" must be a non-negative decimal string.', $value));
        }
    }
}
