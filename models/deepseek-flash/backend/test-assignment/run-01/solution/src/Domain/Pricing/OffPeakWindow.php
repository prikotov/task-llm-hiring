<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Provider-level discount window expressed in UTC minutes of the day.
 * The start is inclusive, the end is exclusive; windows may cross midnight.
 */
final readonly class OffPeakWindow
{
    private function __construct(
        private int $startMinute,
        private int $endMinute,
        private Decimal $multiplier,
    ) {
    }

    public static function fromStrings(string $windowStart, string $windowEnd, string $multiplier): self
    {
        $multiplierValue = Decimal::fromString($multiplier);
        if ($multiplierValue->isNegative()) {
            throw new InvalidArgumentException('Off-peak multiplier must be non-negative.');
        }

        return new self(
            self::parseMinuteOfDay($windowStart),
            self::parseMinuteOfDay($windowEnd),
            $multiplierValue,
        );
    }

    public function multiplier(): Decimal
    {
        return $this->multiplier;
    }

    public function windowStart(): string
    {
        return self::formatMinuteOfDay($this->startMinute);
    }

    public function windowEnd(): string
    {
        return self::formatMinuteOfDay($this->endMinute);
    }

    public function contains(DateTimeImmutable $moment): bool
    {
        $utc = $moment->setTimezone(new DateTimeZone('UTC'));
        $minute = ((int) $utc->format('G')) * 60 + (int) $utc->format('i');

        if ($this->startMinute < $this->endMinute) {
            return $minute >= $this->startMinute && $minute < $this->endMinute;
        }

        if ($this->startMinute > $this->endMinute) {
            return $minute >= $this->startMinute || $minute < $this->endMinute;
        }

        // [x, x) is an empty interval.
        return false;
    }

    private static function parseMinuteOfDay(string $value): int
    {
        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $matches) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid time-of-day value "%s", expected HH:MM.', $value));
        }

        return ((int) $matches[1]) * 60 + (int) $matches[2];
    }

    private static function formatMinuteOfDay(int $minute): string
    {
        return sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
    }
}
