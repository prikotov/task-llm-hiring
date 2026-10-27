<?php

declare(strict_types=1);

namespace App\Domain\Provider;

use App\Domain\Money\Cost;
use InvalidArgumentException;

/**
 * Окно оф-пик провайдера в минутах от 00:00 UTC + множитель.
 *
 * Окно: [start, end) минут от начала суток; окно может переходить
 * через полночь (start > end).
 */
final readonly class OffPeakWindow
{
    private int $startMinutes;
    private int $endMinutes;

    public function __construct(
        private string $windowStart,
        private string $windowEnd,
        private string $multiplier,
    ) {
        $this->startMinutes = self::parseHm($windowStart);
        $this->endMinutes = self::parseHm($windowEnd);

        if (!preg_match('/^\d+(\.\d+)?$/', $multiplier)) {
            throw new InvalidArgumentException(sprintf('Некорректный множитель оф-пик: %s', $multiplier));
        }
    }

    public function appliesToUtcTimestamp(Cost $cost, \DateTimeImmutable $ts): Cost
    {
        $utc = new \DateTimeImmutable($ts->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
        $minutes = (int) $utc->format('G') * 60 + (int) $utc->format('i');

        $inWindow = $this->startMinutes <= $this->endMinutes
            ? ($minutes >= $this->startMinutes && $minutes < $this->endMinutes)
            : ($minutes >= $this->startMinutes || $minutes < $this->endMinutes);

        if (!$inWindow) {
            return $cost;
        }

        return $cost->multiplyBy($this->multiplier);
    }

    private static function parseHm(string $hm): int
    {
        if (!preg_match('/^(\d{1,2}):(\d{1,2})$/', $hm, $m)) {
            throw new InvalidArgumentException(sprintf('Некорректное время HH:MM: %s', $hm));
        }
        $h = (int) $m[1];
        $min = (int) $m[2];
        if ($h > 23 || $min > 59) {
            throw new InvalidArgumentException(sprintf('Некорректное время HH:MM: %s', $hm));
        }

        return $h * 60 + $min;
    }
}
