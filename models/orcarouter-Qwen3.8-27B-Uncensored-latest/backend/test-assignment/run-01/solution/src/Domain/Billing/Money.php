<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Точная десятичная арифметика денег на bcmath (float запрещён для денег).
 *
 * Все суммы переданы — десятичные строки. Промежуточные значения считаются
 * с запасом точности SCALE знаков, без округления; одно единственное
 * округление — {@see self::roundHalfUpTo6()}, по правилу HALF_UP.
 */
final class Money
{
    /** Запас точности для промежуточных вычислений (≥ 16 по заданию). */
    public const SCALE = 20;

    /** Итоговая точность стоимости события/отчёта. */
    public const FINAL_SCALE = 6;

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    /** cost = tokens * rate / 1_000_000, без округления. */
    public static function perMillion(int $tokens, string $rate): string
    {
        return bcdiv(bcmul((string) $tokens, $rate, self::SCALE), '1000000', self::SCALE);
    }

    /**
     * Округление ровно до 6 знаков по правилу HALF_UP (2.5 -> 3).
     * Вход — неотрицательная десятичная строка с ограниченной точностью.
     */
    public static function roundHalfUpTo6(string $value): string
    {
        $value = self::normalize($value);
        if (bccomp($value, '0', self::SCALE) < 0) {
            throw new \InvalidArgumentException('Money rounding expects a non-negative value, got: '.$value);
        }

        // floor(value * 10^6 + 0.5): одна цифра после точки достаточна,
        // т.к. решение порога 0.5 определяется именно первой дробной цифрой.
        $scaled = bcadd(bcmul($value, '1000000', 1), '0.5', 1);
        $integerPart = explode('.', $scaled, 2)[0];
        $integerPart = ltrim($integerPart, '0') ?: '0';

        return bcadd(bcdiv($integerPart, '1000000', self::FINAL_SCALE), '0', self::FINAL_SCALE);
    }

    /** Формат строго с 6 знаками после точки (для выводимых сумм/дельт). */
    public static function format6(string $value): string
    {
        $value = self::normalize($value);
        return bcadd($value, $value[0] === '-' ? '-0' : '0', self::FINAL_SCALE);
        // bcadd с '-0'/'0' не меняет знаковое значение, но нормализует масштаб.
    }

    /**
     * Сокращает строгу до корректной десятичной строки (убирает хвостовые нули
     * сверх точности) — для устойчивости сравнений через bccomp.
     */
    private static function normalize(string $value): string
    {
        return bcadd($value, '0', self::SCALE);
    }
}
