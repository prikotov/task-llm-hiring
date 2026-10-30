<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InputError;

/**
 * Вспомогательные проверки входных данных (общие для парсеров).
 */
final class Parsing
{
    private function __construct()
    {
    }

    /**
     * ISO-8601. По спецификации ts — всегда UTC («Z»); возвращаем
     * DateTimeImmutable со стеночными часами в UTC (подходит для
     * хранения в колонке WITHOUT TIME ZONE).
     */
    public static function iso8601Utc(string $value): \DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            throw InputError::because(string: 'Таймстамп пустой или не строка');
        }

        try {
            $dt = new \DateTimeImmutable($value);
        } catch (\Exception) {
            throw InputError::because(string: 'Некорректный ISO-8601 таймстамп: ' . $value);
        }

        return $dt->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * Обязательная строка непустая.
     */
    public static function requireString(array $arr, string $key, string $context): string
    {
        if (!array_key_exists($key, $arr) || !is_string($arr[$key]) || trim($arr[$key]) === '') {
            throw InputError::because(sprintf('%s: поле "%s" обязательно и должно быть строкой', $context, $key));
        }

        return trim($arr[$key]);
    }

    /**
     * Общее для ставок и множителя: строка-decimal (не число), > 0.
     */
    public static function requireDecimalString(array $arr, string $key, string $context): string
    {
        if (!array_key_exists($key, $arr) || !is_string($arr[$key])) {
            throw InputError::because(sprintf('%s: ставка "%s" обязательна и должна быть строкой', $context, $key));
        }

        $value = trim($arr[$key]);
        if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            throw InputError::because(sprintf('%s: ставка "%s" не корректное десятичное число', $context, $key));
        }

        if (bccomp($value, '0', 0) < 0) {
            throw InputError::because(sprintf('%s: ставка "%s" не может быть отрицательной', $context, $key));
        }

        return $value;
    }

    /**
     * Количество токенов: целое >= 0.
     */
    public static function requireNonNegativeInt(array $arr, string $key, string $context): int
    {
        if (!array_key_exists($key, $arr) || !is_int($arr[$key])) {
            if (array_key_exists($key, $arr) && is_string($arr[$key]) && ctype_digit($arr[$key])) {
                return (int) $arr[$key];     // позволяем целое как строку
            }
            throw InputError::because(sprintf('%s: поле "%s" обязательно и должно быть целым >= 0', $context, $key));
        }

        if ($arr[$key] < 0) {
            throw InputError::because(sprintf('%s: поле "%s" не может быть отрицательным', $context, $key));
        }

        return $arr[$key];
    }
}
