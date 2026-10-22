<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Money;

/**
 * A provider off-peak window over the UTC time of day.
 *
 * The window is [start, end); it may cross midnight (e.g. 16:30–00:30).
 */
final class OffPeak
{
    /** @var positive decimal string */
    private readonly string $multiplier;

    /** @var int minutes from UTC midnight */
    private readonly int $startMinutes;

    /** @var int minutes from UTC midnight */
    private readonly int $endMinutes;

    public function __construct(string $windowStart, string $windowEnd, string $multiplier)
    {
        $this->startMinutes = self::toMinutes($windowStart);
        $this->endMinutes = self::toMinutes($windowEnd);

        if ($multiplier === '' || \bccomp($multiplier, '0', 4) <= 0) {
            throw new \InvalidArgumentException('off_peak multiplier must be positive');
        }

        $this->multiplier = $multiplier;
    }

    public function contains(\DateTimeImmutable $utc): bool
    {
        $minutes = (int) $utc->format('G') * 60 + (int) $utc->format('i');

        if ($this->startMinutes < $this->endMinutes) {
            return $minutes >= $this->startMinutes && $minutes < $this->endMinutes;
        }

        // Window crosses midnight (or is empty when start === end).
        return $this->startMinutes !== $this->endMinutes
            && ($minutes >= $this->startMinutes || $minutes < $this->endMinutes);
    }

    public function apply(string $cost): string
    {
        return Money::mul($cost, $this->multiplier);
    }

    public function getMultiplier(): string
    {
        return $this->multiplier;
    }

    private static function toMinutes(string $hhmm): int
    {
        if (\preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hhmm) !== 1) {
            throw new \InvalidArgumentException(\sprintf('Invalid HH:MM window time "%s"', $hhmm));
        }

        return ((int) \substr($hhmm, 0, 2)) * 60 + (int) \substr($hhmm, 3, 2);
    }

}
