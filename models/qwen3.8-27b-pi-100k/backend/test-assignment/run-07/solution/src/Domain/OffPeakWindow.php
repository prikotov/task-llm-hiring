<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера по UTC-времени суток: [start, end),
 * окно может переходить через полночь (например 16:30–00:30).
 * Секунды и дробная часть момента игнорируются — окно задано минутами.
 */
final class OffPeakWindow
{
    public function __construct(
        private readonly string $windowStart,
        private readonly string $windowEnd,
        private readonly string $multiplier,
    ) {
        if (!self::isValidHHMM($windowStart) || !self::isValidHHMM($windowEnd)) {
            throw new InvalidArgumentException('Окно оф-пика должно быть задано в формате HH:MM');
        }

        if (bccomp($multiplier, '0', 30) <= 0) {
            throw new InvalidArgumentException('Множитель оф-пика должен быть > 0');
        }
    }

    /**
     * Попадает ли UTC-момент в окно [start, end) — с учётом перехода через полночь.
     */
    public function appliesTo(\DateTimeInterface $tsUtc): bool
    {
        $minutes = (int) $tsUtc->format('G') * 60 + (int) $tsUtc->format('i');
        $start = self::toMinutesOfDay($this->windowStart);
        $end = self::toMinutesOfDay($this->windowEnd);

        if ($start === $end) {
            return false; // вырожденное окно — пустое
        }

        return $start < $end
            ? $minutes >= $start && $minutes < $end
            : $minutes >= $start || $minutes < $end;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function windowStart(): string
    {
        return $this->windowStart;
    }

    public function windowEnd(): string
    {
        return $this->windowEnd;
    }

    public static function isValidHHMM(string $value): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value) === 1;
    }

    public static function toMinutesOfDay(string $hhmm): int
    {
        return ((int) substr($hhmm, 0, 2)) * 60 + ((int) substr($hhmm, 3, 2));
    }
}
