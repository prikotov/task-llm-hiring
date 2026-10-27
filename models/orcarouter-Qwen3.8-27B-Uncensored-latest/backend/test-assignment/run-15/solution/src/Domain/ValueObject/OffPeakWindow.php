<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class OffPeakWindow
{
    /**
     * @param int          $startMinutes minutes from midnight (UTC), inclusive
     * @param int          $endMinutes   minutes from midnight (UTC), exclusive
     * @param string       $multiplier   decimal multiplier, e.g. "0.5"
     */
    public function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {
    }

    public static function fromPayload(string $windowStart, string $windowEnd, string $multiplier): self
    {
        $start = self::parseHm($windowStart);
        $end = self::parseHm($windowEnd);
        return new self($start, $end, $multiplier);
    }

    /** [16:30, 00:30) — window may cross midnight. */
    public function containsUtc(\DateTimeImmutable $utc): bool
    {
        $minutes = $utc->format('H') * 60 + (int) $utc->format('i');
        if ($this->startMinutes <= $this->endMinutes) {
            return $minutes >= $this->startMinutes && $minutes < $this->endMinutes;
        }
        return $minutes >= $this->startMinutes || $minutes < $this->endMinutes;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function startMinutesForSerialization(): int
    {
        return $this->startMinutes;
    }

    public function endMinutesForSerialization(): int
    {
        return $this->endMinutes;
    }

    private static function parseHm(string $value): int
    {
        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $value) !== 1) {
            throw new \InvalidArgumentException("Invalid HH:MM window bound: {$value}");
        }
        [$h, $m] = explode(':', $value);
        return (int) $h * 60 + (int) $m;
    }
}
