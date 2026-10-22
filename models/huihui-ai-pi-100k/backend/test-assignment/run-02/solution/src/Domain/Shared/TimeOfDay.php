<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Время суток (минуты от 00:00) и разбор формата HH:MM.
 * Вся арифметика — в UTC; окна скидки задаются по UTC.
 */
final class TimeOfDay
{
    private function __construct()
    {
    }

    /**
     * @throws InvalidArgumentException при неверном формате (ожидается HH:MM, UTC)
     */
    public static function parseHhmm(string $value, string $context): int
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $matches)) {
            throw new InvalidArgumentException(sprintf('%s: expected "HH:MM" (UTC), got "%s"', $context, $value));
        }

        return ((int) $matches[1]) * 60 + ((int) $matches[2]);
    }

    /**
     * Минуты текущего дня UTC для момента времени.
     */
    public static function minutesOfDayUtc(DateTimeImmutable $utc): int
    {
        // Приводим к UTC-представлению, чтобы часы/минуты были часами UTC.
        $utc = $utc->setTimezone(new \DateTimeZone('UTC'));

        return $utc->format('G') * 60 + (int) $utc->format('i');
    }
}
