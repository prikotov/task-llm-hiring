<?php

declare(strict_types=1);

namespace App\Shared;

use DateTimeImmutable;
use DateTimeZone;

/** Вспомогательные проверки и парсинг входных данных импорта. */
final class InputValidator
{
    private const DECIMAL_RE = '/^\d{1,15}(\.\d{1,6})?$/';
    private const TIME_RE = '/^([01]\d|2[0-3]):[0-5]\d$/';

    private function __construct()
    {
    }

    public static function requireString(array $data, string $key, string $ctx): string
    {
        if (!isset($data[$key]) || !is_string($data[$key]) || trim($data[$key]) === '') {
            throw new ImportException(sprintf('%s: ожидается непустая строка поля "%s"', $ctx, $key));
        }

        return $data[$key];
    }

    public static function requireStringArray(array $data, string $key, string $ctx): array
    {
        if (!isset($data[$key])) {
            return [];
        }
        if (!is_array($data[$key])) {
            throw new ImportException(sprintf('%s: поле "%s" должно быть массивом', $ctx, $key));
        }

        return $data[$key];
    }

    public static function decimal(string $value, string $ctx): string
    {
        if (!is_string($value) || preg_match(self::DECIMAL_RE, $value) !== 1) {
            throw new ImportException(sprintf('%s: некорректное десятичное значение "%s" (ожидается строка >= 0, до 6 знаков)', $ctx, (string) $value));
        }

        return bcadd($value, '0', 6);
    }

    public static function hhmm(string $value, string $ctx): string
    {
        if (!is_string($value) || preg_match(self::TIME_RE, $value) !== 1) {
            throw new ImportException(sprintf('%s: некорректное время "%s" (ожидается HH:MM, 00:00–23:59)', $ctx, (string) $value));
        }

        return $value;
    }

    public static function utcDateTime(string $value, string $ctx): DateTimeImmutable
    {
        try {
            $dt = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException(sprintf('%s: некорректная дата/время "%s" (ожидается ISO-8601)', $ctx, $value));
        }
        if ($dt->getTimezone()->getOffset(new DateTimeImmutable('@'.(string) $dt->getTimestamp())) !== 0) {
            throw new ImportException(sprintf('%s: дата/время "%s" должно быть в UTC', $ctx, $value));
        }

        return $dt;
    }

    public static function nonNegativeInt(mixed $value, string $key, string $ctx): int
    {
        if (!is_int($value) || $value < 0) {
            throw new ImportException(sprintf('%s: поле "%s" должно быть целым числом >= 0', $ctx, $key));
        }

        return $value;
    }
}
