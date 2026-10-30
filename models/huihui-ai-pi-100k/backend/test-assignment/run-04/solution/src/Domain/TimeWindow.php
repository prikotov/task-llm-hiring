<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Проверка попадания момента времени (UTC) в окно вида [start, end) в формате "HH:MM".
 * Окно может переходить через полночь (например, "16:30"–"00:30").
 * При start == end окно считается пустым.
 */
final class TimeWindow
{
    public const HH_MM = '/^\d{2}:\d{2}$/';

    public static function minutesOfUtc(DateTimeImmutable $utc): int
    {
        $utc = $utc->setTimezone(new DateTimeZone('UTC'));

        return (int) $utc->format('H') * 60 + (int) $utc->format('i');
    }

    public static function contains(DateTimeImmutable $utc, string $startHhMm, string $endHhMm): bool
    {
        $t = self::minutesOfUtc($utc);
        $start = (int) substr($startHhMm, 0, 2) * 60 + (int) substr($startHhMm, 4, 2);
        $end = (int) substr($endHhMm, 0, 2) * 60 + (int) substr($endHhMm, 4, 2);

        if ($start === $end) {
            return false;
        }

        return $start < $end
            ? ($t >= $start && $t < $end)
            : ($t >= $start || $t < $end);
    }
}
