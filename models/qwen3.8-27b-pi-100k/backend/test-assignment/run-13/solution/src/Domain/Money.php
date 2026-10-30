<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика для денег (bcmath, масштаб >= 16).
 * Тип float запрещён.
 *
 * bcmath::bc_mul/bc_div с положительным scale используют округление
 * ROUND_HALF_UP (2.5 → 3), что соответствует правилу HALF_UP.
 */
final readonly class Money
{
    public const SCALE = 16;

    /**
     * Умножение, масштаб SELF::SCALE (без округления ниже масштаба).
     */
    public static function mul(string $a, string $b): string
    {
        return \bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        return \bcdiv($a, $b, self::SCALE);
    }

    public static function add(string $a, string $b): string
    {
        return \bcadd($a, $b, self::SCALE);
    }

    /**
     * Округление до $scale знаков, HALF_UP. bcmath с положительным scale
     * использует ROUND_HALF_UP по умолчанию.
     */
    public static function round(string $value, int $scale = 6): string
    {
        $scale = \max(0, \min($scale, self::SCALE));

        return \bcround($value, $scale);
    }

    /**
     * Нормализация десятичной строки: убирает хвостовые нули,
     * валидирует формат.
     */
    public static function format(string $value): string
    {
        if (! preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Некорректное десятичное число: "%s"', $value));
        }

        if (str_contains($value, '.')) {
            $value = rtrim($value, '0');
            $value = rtrim($value, '.');
        }

        return $value === '' || $value === '-' ? '0' : $value;
    }

    /**
     * Форматирование для вывода: ровно $scale знаков после точки.
     */
    public static function toFixed(string $value, int $scale = 6): string
    {
        if (! preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Некорректное десятичное число: "%s"', $value));
        }

        $parts = explode('.', $value, 2);
        $int = $parts[0];
        $frac = $parts[1] ?? '';
        $frac = str_pad(substr($frac, 0, $scale), $scale, '0');

        return ($int === '' ? '0' : $int) . ($scale > 0 ? '.' . $frac : '');
    }
}
