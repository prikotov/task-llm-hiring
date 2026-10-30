<?php

declare(strict_types=1);

namespace App\Domain\Time;

/**
 * Строгий разбор ISO-8601-моментов в UTC (суффикс «Z» обязателен,
 * смещения типа +02:00 не принимаются).
 * Формат: YYYY-MM-DDTHH:MM:SS[.fff]Z
 */
final class UtcInstant
{
    private const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{1,6})?Z$/';

    public static function parse(string $value): \DateTimeImmutable
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" is not a UTC ISO-8601 instant (expected like 2026-08-01T00:00:00Z).',
                $value,
            ));
        }

        $dt = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));

        // Защита от «катушек» невалидных календарных дат (например 2026-02-30)
        if ($dt->format('Y-m-d\TH:i:s') !== substr($value, 0, 19)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" is not a valid calendar date-time.',
                $value,
            ));
        }

        return $dt;
    }
}
