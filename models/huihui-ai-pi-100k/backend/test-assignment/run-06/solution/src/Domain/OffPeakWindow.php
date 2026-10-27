<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Represents an off-peak discount window for a provider.
 * Window is defined in UTC time-of-day as [start, end).
 * The window may cross midnight.
 */
final class OffPeakWindow
{
    private int $startMinutes;
    private int $endMinutes;
    private string $multiplier;

    public function __construct(string $windowStart, string $windowEnd, string $multiplier)
    {
        $this->startMinutes = self::parseTimeToMinutes($windowStart);
        $this->endMinutes = self::parseTimeToMinutes($windowEnd);
        $this->multiplier = $multiplier;
    }

    /**
     * Check if a time of day (in minutes from midnight) falls within the off-peak window.
     */
    public function contains(int $timeOfDayMinutes): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false; // Degenerate window
        }

        if ($this->startMinutes < $this->endMinutes) {
            // Normal window: [start, end)
            return $timeOfDayMinutes >= $this->startMinutes && $timeOfDayMinutes < $this->endMinutes;
        }

        // Crosses midnight: [start, 24:00) ∪ [00:00, end)
        return $timeOfDayMinutes >= $this->startMinutes || $timeOfDayMinutes < $this->endMinutes;
    }

    public function getMultiplier(): string
    {
        return $this->multiplier;
    }

    private static function parseTimeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid time format: {$time}");
        }
        $hours = (int) $parts[0];
        $minutes = (int) $parts[1];
        if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59) {
            throw new \InvalidArgumentException("Invalid time value: {$time}");
        }
        return $hours * 60 + $minutes;
    }
}
