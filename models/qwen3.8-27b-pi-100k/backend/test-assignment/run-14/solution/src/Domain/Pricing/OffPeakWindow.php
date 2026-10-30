<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use InvalidArgumentException;

/**
 * Окно оф-пика провайдера (UTC-время суток).
 *
 * window_start включительно, window_end исключительно; окно может
 * переходить через полночь (например 16:30–00:30).
 */
final readonly class OffPeakWindow
{
    public function __construct(
        public string $windowStart, // HH:MM
        public string $windowEnd,   // HH:MM
        public string $multiplier,  // десятичная строка, например "0.5"
    ) {
        if (!preg_match('/^\d{2}:\d{2}$/', $windowStart) || !preg_match('/^\d{2}:\d{2}$/', $windowEnd)) {
            throw new InvalidArgumentException('Off-peak window must be in HH:MM format.');
        }
    }

    /**
     * Проверяет, попадает ли момент дня (UTC) в окно [start, end).
     *
     * @param string $hhmm время суток события, формат HH:MM
     */
    public function contains(string $hhmm): bool
    {
        $t = self::toMinutes($hhmm);
        $start = self::toMinutes($this->windowStart);
        $end = self::toMinutes($this->windowEnd);

        if ($start <= $end) {
            return $start <= $t && $t < $end;
        }

        return $t >= $start || $t < $end;
    }

    private static function toMinutes(string $hhmm): int
    {
        [$h, $m] = explode(':', $hhmm);

        return ((int) $h) * 60 + (int) $m;
    }
}
