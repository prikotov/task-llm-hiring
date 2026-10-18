<?php

declare(strict_types=1);

namespace App\Support;

use App\Shared\Exception\BadInputException;

/**
 * Время суток в формате «HH:MM» (UTC) → минуты от полуночи (0..1439).
 */
final class TimeOfDay
{
    private const PATTERN = '/^([01]\d|2[0-3]):([0-5]\d)$/';

    public static function parseHhMm(string $value): int
    {
        if (preg_match(self::PATTERN, $value, $m) !== 1) {
            throw new BadInputException(sprintf(
                'Некорректное время суток (ожидался формат HH:MM, например 16:30): «%s»',
                $value,
            ));
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }
}
