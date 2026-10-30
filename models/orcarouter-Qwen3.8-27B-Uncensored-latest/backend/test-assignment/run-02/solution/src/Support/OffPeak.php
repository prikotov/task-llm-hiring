<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;

/**
 * Логика оф-пикового окна провайдера.
 *
 * Окно задается на уровне провайдера по времени суток UTC: [start, end),
 * старт включительно, конец исключительно. Окно может пересекать полночь
 * (напр. 16:30–00:30).
 */
final class OffPeak
{
    /**
     * Попадает ли время суток (UTC) события в оф-пиковое окно.
     */
    public static function isInWindow(DateTimeImmutable $utc, ?string $start, ?string $end): bool
    {
        if ($start === null || $end === null) {
            return false;
        }
        $s = self::minutesOfHour($start);
        $e = self::minutesOfHour($end);
        if ($s === null || $e === null || $s === $e) {
            return false;
        }
        $t = self::minutesOfDay($utc);
        if ($s < $e) {
            return $t >= $s && $t < $e;
        }
        // окно пересекает полночь
        return $t >= $s || $t < $e;
    }

    /** Минуты в сутках для UTC-момента. */
    public static function minutesOfDay(DateTimeImmutable $utc): int
    {
        $z = $utc->setTimezone(new \DateTimeZone('UTC'));
        return $z->format('G') * 60 + (int) $z->format('i');
    }

    /** "HH:MM" -> минуты в сутках (0..1440) или null, если формат неверный. */
    public static function minutesOfHour(string $hhmm): ?int
    {
        if ($hhmm === '24:00') {
            return 1440;
        }
        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $hhmm, $m) !== 1) {
            return null;
        }
        return ((int) $m[1]) * 60 + ((int) $m[2]);
    }
}
