<?php

declare(strict_types=1);

namespace App\Import;

use stdClass;

/** Общие правила валидации JSON-полей входных файлов. */
abstract class AbstractJsonParser
{
    /** Неотрицательное десятичное число строкой (ставка, множитель). */
    protected const DECIMAL_PATTERN = '/^\d{1,20}(\.\d{1,12})?$/';

    /** Временем суток UTC: HH:MM, 00:00–23:59. */
    protected const CLOCK_PATTERN = '/^([01]\d|2[0-3]):([0-5]\d)$/';

    /**
     * Возвращает непустую строку по ключу объекта.
     *
     * @param object|stdClass $row
     */
    protected function string(object $row, string $key, string $ctx): string
    {
        $value = $row->{$key} ?? null;
        if (!\is_string($value) || trim($value) === '') {
            throw new ImportException(sprintf('%s: поле "%s" должно быть непустой строкой.', $ctx, $key));
        }

        return $value;
    }

    /**
     * Возвращает целое ≥ 0 (JSON-число или строка-целое до 18 знаков).
     *
     * @param object|stdClass $row
     */
    protected function nonNegativeInt(object $row, string $key, string $ctx): int
    {
        $value = $row->{$key} ?? null;
        if (\is_int($value) && $value >= 0) {
            return $value;
        }
        if (\is_string($value) && preg_match('/^\d{1,18}$/', $value) === 1) {
            return (int) $value;
        }

        throw new ImportException(sprintf('%s: поле "%s" должно быть целым числом ≥ 0.', $ctx, $key));
    }

    protected function decimalString(object $row, string $key, string $ctx): string
    {
        $value = $row->{$key} ?? null;
        if (\is_string($value) && preg_match(self::DECIMAL_PATTERN, $value) === 1) {
            return $value;
        }

        throw new ImportException(sprintf('%s: поле "%s" должно быть неотрицательной десятичной строкой (например, "3.000000").', $ctx, $key));
    }

    /** Минуты суток UTC из строки HH:MM. */
    protected function clockToMinutes(string $clock, string $ctx): int
    {
        if (preg_match(self::CLOCK_PATTERN, $clock, $m) !== 1) {
            throw new ImportException(sprintf('%s: время окна должно задаваться в формате HH:MM (UTC), получено "%s".', $ctx, $clock));
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }
}
