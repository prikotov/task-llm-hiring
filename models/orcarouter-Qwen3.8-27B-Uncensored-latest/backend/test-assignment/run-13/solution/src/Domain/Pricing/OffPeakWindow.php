<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

final class OffPeakWindow
{
    /**
     * Window on UTC time-of-day: [startMinutes, endMinutes), may cross midnight.
     * Multiplier applies to the whole event cost when the event falls in the window.
     */
    public function __construct(
        public readonly int $startMinutes,
        public readonly int $endMinutes,
        public readonly string $multiplier, // decimal string
    ) {
        if ($startMinutes < 0 || $startMinutes >= 1440) {
            throw new \InvalidArgumentException('Start minutes out of range.');
        }
        if ($endMinutes < 0 || $endMinutes >= 1440) {
            throw new \InvalidArgumentException('End minutes out of range.');
        }
    }

    public function inWindow(int $minutesOfDay): bool
    {
        if ($this->startMinutes <= $this->endMinutes) {
            return $minutesOfDay >= $this->startMinutes && $minutesOfDay < $this->endMinutes;
        }
        // crosses midnight
        return $minutesOfDay >= $this->startMinutes || $minutesOfDay < $this->endMinutes;
    }

    public static function fromHhmm(string $startHhmm, string $endHhmm, string $multiplier): self
    {
        return new self(self::toMinutes($startHhmm), self::toMinutes($endHhmm), $multiplier);
    }

    private static function toMinutes(string $hhmm): int
    {
        if (preg_match('/^(\d{2}):(\d{2})$/', $hhmm, $m) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid HH:MM: "%s"', $hhmm));
        }
        $h = (int) $m[1];
        $mm = (int) $m[2];
        if ($h >= 24 || $mm >= 60) {
            throw new \InvalidArgumentException(sprintf('Invalid HH:MM: "%s"', $hhmm));
        }

        return $h * 60 + $mm;
    }
}
