<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

/**
 * Окно оф-пиковой скидки провайдера (UTC, по времени суток).
 *
 * Окно задаётся на уровне провайдера: включительно window_start,
 * исключительно window_end; может пересекать полночь.
 */
final class OffPeak
{
    public function __construct(
        private readonly string $windowStart, // "HH:MM"
        private readonly string $windowEnd,   // "HH:MM"
        private readonly string $multiplier,  // десятичная строка, напр. "0.5"
    ) {
    }

    public function getString(string $prop): string
    {
        return match ($prop) {
            'windowStart' => $this->windowStart,
            'windowEnd' => $this->windowEnd,
            'multiplier' => $this->multiplier,
            default => throw new \InvalidArgumentException("unknown prop {$prop}"),
        };
    }

    public function windowStart(): string
    {
        return $this->windowStart;
    }

    public function windowEnd(): string
    {
        return $this->windowEnd;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    /**
     * Попадает ли время суток (UTC) события в окно скидки.
     */
    public function contains(DateTimeImmutable $ts): bool
    {
        $startSec = self::hhmmToSeconds($this->windowStart);
        $endSec = self::hhmmToSeconds($this->windowEnd);
        $tSec = (int) $ts->format('H') * 3600 + (int) $ts->format('i') * 60 + (int) $ts->format('s');

        if ($startSec === $endSec) {
            // Degenerate окно — трактуем как отсутствие скидки.
            return false;
        }

        // Окно без пересечения полуночи: [start, end).
        if ($startSec < $endSec) {
            return $tSec >= $startSec && $tSec < $endSec;
        }

        // Окно перескает полночь: [start, 24h) ∪ [0, end).
        return $tSec >= $startSec || $tSec < $endSec;
    }

    private static function hhmmToSeconds(string $hhmm): int
    {
        $parts = explode(':', $hhmm);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("invalid time format: {$hhmm}");
        }

        return ((int) $parts[0]) * 3600 + ((int) $parts[1]) * 60;
    }
}
