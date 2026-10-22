<?php

declare(strict_types=1);

namespace App\Domain\Common;

use DateTimeImmutable;

use function checkdate;
use function date;
use function mktime;

/** Валидация входных данных импорта: общие помощники. */
final class Input
{
    public const DECIMAL_PATTERN = '/^\d+(\.\d+)?$/';

    public const TIME_HHMM_PATTERN = '/^(2[0-3]|1\d|[01]\d):[0-5]\d$/';

    public const ISO_UTC_PATTERN = '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{1,9})?Z$/';

    public static function daysInMonth(int $month, int $year): int
    {
        return checkdate($month, 1, $year) ? (int) date('t', mktime(0, 0, 0, $month, 1, $year)) : 0;
    }

    /** @return int минут от полуночи */
    public static function hhmmToMinutes(string $hhmm): int
    {
        [$h, $m] = explode(':', $hhmm);

        return (int) $h * 60 + (int) $m;
    }

    /**
     * Парсит ISO-8601 с суффиксом Z (UTC, секунды, опционально дробные)
     * в DateTimeImmutable; null, если строка невалидна.
     *
     * Компоненты проверяются явно, т.к. DateTimeImmutable молча «чинит»
     * переполненные даты (напр. 2026-02-30).
     */
    public static function parseIsoUtc(string $value): ?DateTimeImmutable
    {
        if (preg_match(self::ISO_UTC_PATTERN, $value, $m) !== 1) {
            return null;
        }

        [, $year, $month, $day, $hour, $minute, $second] = $m;
        if ($month < 1 || $month > 12
            || $day < 1 || $day > self::daysInMonth((int) $month, (int) $year)
            || $hour > 23
            || $minute > 59
            || $second > 59
        ) {
            return null;
        }

        $micros = 0;
        if (isset($m[7]) && $m[7] !== '') {
            $frac = substr(ltrim($m[7], '.'), 0, 6);
            $micros = (int) str_pad($frac, 6, '0');
        }

        return new DateTimeImmutable(sprintf(
            '%04d-%02d-%02d %02d:%02d:%02d.%06d',
            (int) $year,
            (int) $month,
            (int) $day,
            (int) $hour,
            (int) $minute,
            (int) $second,
            $micros,
        ));
    }

    /** Строка "HH:MM"? */
    public static function isHhmm(string $value): bool
    {
        return preg_match(self::TIME_HHMM_PATTERN, $value) === 1;
    }

    /** Неотрицательное десятичное число (строкой)? */
    public static function isDecimalString(string $value, int $maxFraction = 6): bool
    {
        if (preg_match(self::DECIMAL_PATTERN, $value) !== 1) {
            return false;
        }

        if (str_contains($value, '.')) {
            return strlen(substr($value, strrpos($value, '.') + 1)) <= $maxFraction;
        }

        return true;
    }

    /** Элемент JSON — объект? */
    public static function asRow(mixed $value, string $label): array
    {
        if (!\is_array($value) || $value === []) {
            throw new ImportFormatException($label.': ожидается объект JSON');
        }

        return $value;
    }

    /** @throws ImportFormatException строка пуста */
    public static function asNonEmptyString(mixed $value, string $label): string
    {
        if (!\is_string($value) || $value === '') {
            throw new ImportFormatException($label.': ожидается непустая строка');
        }

        return $value;
    }

    /** Нейтрицательное целое (из JSON)? */
    public static function asNonNegativeInt(mixed $value, string $label): int
    {
        if (!\is_int($value) || $value < 0) {
            throw new ImportFormatException($label.': ожидается целое >= 0');
        }

        return $value;
    }
}
