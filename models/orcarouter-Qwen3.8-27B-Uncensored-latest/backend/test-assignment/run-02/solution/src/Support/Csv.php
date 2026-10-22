<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Минимальный, предсказуемый CSV-форматтер (разделитель ",", перевод строки
 * "\n", кодировка UTF-8). Экранирование по RFC 4180 только там, где нужно,
 * чтобы вывод совпадал с примерами задания байт-в-байт.
 */
final class Csv
{
    public static function row(array $fields): string
    {
        return implode(',', array_map(self::field(...), $fields));
    }

    public static function field(string $value): string
    {
        if (preg_match('/[",\n\r]/', $value) === 1) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }
}
