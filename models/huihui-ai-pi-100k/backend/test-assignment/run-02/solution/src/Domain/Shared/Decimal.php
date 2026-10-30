<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use RuntimeException;

/**
 * Утилиты точной десятичной арифметики на bcmath (float для денег запрещён).
 *
 * Все строки — канонические десятичные числа: опциональный «-», целая часть
 * (без ведущих нулей), опциональная дробная часть.
 */
final class Decimal
{
    /** Запас точности для промежуточных операций (ставка до 10 знаков + множители). */
    public const SCALE = 32;

    private function __construct()
    {
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        if (bccomp($b, '0', 0) === 0) {
            throw new RuntimeException('Decimal division by zero');
        }

        return bcdiv($a, $b, self::SCALE);
    }

    public static function add(string $a, string $b): string
    {
        return self::normalize(bcadd($a, $b, self::SCALE));
    }

    public static function sub(string $a, string $b): string
    {
        return self::normalize(bcsub($a, $b, self::SCALE));
    }

    /**
     * @param iterable<string> $values
     */
    public static function sum(iterable $values): string
    {
        $total = '0';
        foreach ($values as $value) {
            $total = self::add($total, $value);
        }

        return $total;
    }

    /**
     * Округление до $digits знаков после точки, HALF_UP (0.5 всегда «вверх по абсолюту»,
     * т.е. 2.5 → 3, -2.5 → -3).
     */
    public static function roundHalfUp(string $value, int $digits = 6): string
    {
        if ($digits < 0) {
            throw new RuntimeException('digits must be >= 0');
        }

        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : self::stripPlus($value);

        $rounded = bcadd(bcmul($abs, self::power10($digits), self::SCALE), '0.5', 0);
        if ($digits > 0) {
            $rounded = bcdiv($rounded, self::power10($digits), $digits);
        }

        return self::normalize($negative ? '-'.$rounded : $rounded);
    }

    /**
     * Приведение к канонической десятичной строке (убирает ведущие/хвостовые нули).
     */
    public static function normalize(string $value): string
    {
        return self::normalizeInternal($value);
    }

    /**
     * Форматирование ровно до 6 знаков после точки (значение должно быть
     * не длиннее 6 знаков; лишние знаки отсекаются, ведущие/хвостовые нули — нормализованы).
     */
    public static function format6(string $value): string
    {
        $value = self::normalizeInternal($value);
        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : self::stripPlus($value);

        if (!preg_match('/^\d+(\.\d+)?$/', $abs)) {
            throw new RuntimeException(sprintf('Cannot format decimal value "%s"', $value));
        }

        if (str_contains($abs, '.')) {
            [$int, $frac] = explode('.', $abs, 2);
            $frac = str_pad(substr($frac, 0, 6), 6, '0');
        } else {
            $int = $abs;
            $frac = '000000';
        }

        $int = ltrim($int, '0');
        if ($int === '') {
            $int = '0';
        }

        // Минус сохраняем даже при нулевой целой части (normalize() уже снял
        // знак у значения, равного нулю).
        return ($negative ? '-' : '').$int.'.'.$frac;
    }

    private static function power10(int $n): string
    {
        $result = '1';
        for ($i = 0; $i < $n; $i++) {
            $result .= '0';
        }

        return $result;
    }

    private static function normalizeInternal(string $value): string
    {
        $value = self::stripPlus($value);
        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : $value;

        if (str_contains($abs, '.')) {
            [$int, $frac] = explode('.', $abs, 2);
            $frac = rtrim($frac, '0');
            $int = ltrim($int, '0') ?: '0';
            $abs = $frac === '' ? $int : $int.'.'.$frac;
        } else {
            $abs = ltrim($abs, '0') ?: '0';
        }

        return ($negative && $abs !== '0' ? '-' : '').$abs;
    }

    private static function stripPlus(string $value): string
    {
        return ltrim($value, '+');
    }
}
