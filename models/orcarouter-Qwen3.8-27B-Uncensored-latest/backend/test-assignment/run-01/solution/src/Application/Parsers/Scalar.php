<?php

declare(strict_types=1);

namespace App\Application\Parsers;

use App\Domain\Exception\ImportException;

/**
 * Общие проверки скалярных значений входных файлов.
 */
final class Scalar
{
    /** "HH:MM", 00:00..23:59. */
    public static function timeOfDay(string $value, string $context): string
    {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value)) {
            throw ImportException::invalid($context, 'время должно быть в формате HH:MM (UTC)');
        }

        return $value;
    }

    /** Неотрицательное десятичное число, 0..6 знаков после точки. */
    public static function decimal(string $value, string $context): string
    {
        if (!preg_match('/^\d+(\.\d{1,6})?$/', $value)) {
            throw ImportException::invalid($context, 'ожидается неотрицательное десятичное число (0..6 знаков после точки)');
        }

        return $value;
    }

    public static function nonEmptyString(mixed $value, string $field, string $context): string
    {
        if (!is_string($value) || trim($value) === '') {
            throw ImportException::invalid($context, sprintf('поле %s должно быть непустой строкой', $field));
        }

        return $value;
    }

    /** Целое количество токенов (JSON int, >= 0; float — нет). */
    public static function tokenCount(mixed $value, string $field, string $context): int
    {
        if (!is_int($value) || $value < 0 || $value > \PHP_INT_MAX) {
            throw ImportException::invalid($context, sprintf('поле %s должно быть целым числом >= 0', $field));
        }

        return $value;
    }

    /** ISO-8601, возвращает момент в UTC (нулевой смещение обязательно). */
    public static function isoUtc(mixed $value, string $context): \DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            throw ImportException::invalid($context, 'ожидается ISO-8601 дата-время (UTC)');
        }

        try {
            $dt = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw ImportException::invalid($context, 'не удалось разобрать ISO-8601 дату-время');
        }

        if ($dt->getTimezone()->getOffset($dt) !== 0 && !preg_match('/Z$/i', trim($value))) {
            throw ImportException::invalid($context, 'дата-время должна быть в UTC');
        }

        return $dt;
    }
}
