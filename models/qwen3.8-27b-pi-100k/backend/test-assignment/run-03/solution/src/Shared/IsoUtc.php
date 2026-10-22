<?php

declare(strict_types=1);

namespace App\Shared;

/**
 * Разбор меток времени в формате ISO-8601, всегда UTC (суффикс Z).
 *
 * Допускается опциональная дробная часть секунды (до 6 знаков,
 * микросекундная точность PostgreSQL timestamptz).
 */
final class IsoUtc
{
    private const PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/D';

    public static function parse(string $value, string $context): \DateTimeImmutable
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InputFormatException($context.': ожидаётся метка времени ISO-8601 в UTC (суффикс Z)');
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new InputFormatException($context.': некорректная дата или время', previous: $e);
        }
    }
}
