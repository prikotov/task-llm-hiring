<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера (время суток UTC).
 *
 * Окно на полуинтервале [start, end): start включительно, end ислючительно.
 * Может пересекать полночь (например 16:30–00:30).
 *
 * @readonly
 */
final readonly class OffPeakWindow
{
    /** @param int<multiplier в масштабе 10^0..> $multiplierMicros */
    public function __construct(
        private int $startMinutes,
        private int $endMinutes,
        private string $multiplier,
    ) {
        $this->validateMinutes($startMinutes);
        $this->validateMinutes($endMinutes);
        if ($startMinutes === $endMinutes) {
            throw new InvalidArgumentException('Off-peak window start and end must differ.');
        }
        if (bccomp($multiplier, '0', 10) <= 0) {
            throw new InvalidArgumentException('Off-peak multiplier must be positive.');
        }
    }

    public static function fromStrings(string $start, string $end, string $multiplier): self
    {
        return new self(
            self::minutesOfDay($start),
            self::minutesOfDay($end),
            $multiplier,
        );
    }

    /** true, если время $minutesOfDay (минуты с начала дня) попадает в окно. */
    public function contains(int $minutesOfDay): bool
    {
        if ($this->startMinutes < $this->endMinutes) {
            return $minutesOfDay >= $this->startMinutes && $minutesOfDay < $this->endMinutes;
        }

        // Окно через полночь: [start, 24:00) ∪ [00:00, end)
        return $minutesOfDay >= $this->startMinutes || $minutesOfDay < $this->endMinutes;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    private function validateMinutes(int $minutes): void
    {
        if ($minutes < 0 || $minutes >= 1440) {
            throw new InvalidArgumentException(sprintf('Minutes of day out of range: %d', $minutes));
        }
    }

    private static function minutesOfDay(string $hhmm): int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $hhmm, $m)) {
            throw new InvalidArgumentException(sprintf('Invalid time-of-day format: "%s" (expected "HH:MM").', $hhmm));
        }

        $hour = (int) $m[1];
        $minute = (int) $m[2];
        if ($hour > 23 || $minute > 59) {
            throw new InvalidArgumentException(sprintf('Invalid time-of-day: "%s".', $hhmm));
        }

        return $hour * 60 + $minute;
    }
}
