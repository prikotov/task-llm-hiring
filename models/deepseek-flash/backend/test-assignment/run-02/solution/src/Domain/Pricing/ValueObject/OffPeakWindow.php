<?php

declare(strict_types=1);

namespace App\Domain\Pricing\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Окно оф-пиковой скидки провайдера в UTC.
 *
 * Границы задаются как HH:MM. Начало входит в окно, конец — нет. Если
 * начало позже конца, окно переходит через полночь.
 */
final readonly class OffPeakWindow
{
    public function __construct(
        private string $start,
        private string $end,
        private string $multiplier,
    ) {
        self::assertTime($start, 'window_start');
        self::assertTime($end, 'window_end');

        if (!preg_match('/^\d+(?:\.\d+)?$/', $multiplier)) {
            throw new InvalidArgumentException(sprintf('Invalid off-peak multiplier "%s".', $multiplier));
        }
    }

    public function start(): string
    {
        return $this->start;
    }

    public function end(): string
    {
        return $this->end;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function contains(DateTimeImmutable $moment): bool
    {
        $utc = $moment->setTimezone(new \DateTimeZone('UTC'));
        $seconds = ((int) $utc->format('H')) * 3600
            + ((int) $utc->format('i')) * 60
            + (int) $utc->format('s');

        $start = self::toSeconds($this->start);
        $end = self::toSeconds($this->end);

        if ($start === $end) {
            return false;
        }

        if ($start < $end) {
            return $seconds >= $start && $seconds < $end;
        }

        return $seconds >= $start || $seconds < $end;
    }

    private static function assertTime(string $value, string $field): void
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $matches)) {
            throw new InvalidArgumentException(sprintf('Invalid %s "%s", expected HH:MM.', $field, $value));
        }
    }

    private static function toSeconds(string $value): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $value));

        return $hours * 3600 + $minutes * 60;
    }
}
