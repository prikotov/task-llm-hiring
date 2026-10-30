<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Форматирование UTC-момента в ISO-8601 с буквой Z (дробная часть — только
 * если она не нулевая), например 2026-09-15T11:00:00Z.
 */
final class TimestampFormatter
{
    public static function formatUtc(\DateTimeInterface $ts): string
    {
        $micro = $ts->format('u');
        $fraction = $micro === '000000' ? '' : '.' . rtrim($micro, '0');

        return $ts->format('Y-m-d\TH:i:s') . $fraction . 'Z';
    }
}
