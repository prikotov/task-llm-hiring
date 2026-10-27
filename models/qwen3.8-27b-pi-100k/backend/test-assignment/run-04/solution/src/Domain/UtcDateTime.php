<?php

declare(strict_types=1);

namespace App\Domain;

use App\Exception\ImportException;

/**
 * Разбор ISO-8601 дат/времен в UTC (суффикс Z — обязателен: входные данные всегда UTC).
 */
final class UtcDateTime
{
    private const PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,9})?Z$/';

    private function __construct() {}

    public static function parse(string $value, string $context): \DateTimeImmutable
    {
        if (!preg_match(self::PATTERN, $value)) {
            throw new ImportException(sprintf(
                '%s: ожидается ISO-8601 с UTC (например 2026-09-01T00:00:00Z), получено "%s"',
                $context,
                $value,
            ));
        }

        try {
            $ts = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException(sprintf('%s: некорректная дата "%s"', $context, $value));
        }

        // PHP «тихо переносит» несуществующие даты (2026-02-30 → 01.03).
        if ($ts->format('Y-m-d') !== substr($value, 0, 10)) {
            throw new ImportException(sprintf('%s: некорректная дата "%s"', $context, $value));
        }

        if ($ts->getOffset() !== 0) {
            throw new ImportException(sprintf('%s: дата не в UTC ("%s")', $context, $value));
        }

        // PHP «тихо» переводит несуществующие даты (2026-02-30 -> 01.03).
        if ($ts->format('Y-m-d') !== substr($value, 0, 10)) {
            throw new ImportException(sprintf('%s: некорректная дата ("%s")', $context, $value));
        }

        return $ts;
    }
}
