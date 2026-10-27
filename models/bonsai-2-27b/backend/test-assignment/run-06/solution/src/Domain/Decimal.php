<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика на bcmath — без float.
 *
 * WORK_SCALE = 18 — запас для всех промежуточных вычислений:
 * цены за 1M токенов (порядок 0.1–10), токены ≤ 10^9 → сумма
 * ≤ ~10^12; 18 знаков более чем достаточно.
 */
final class Decimal
{
    public const WORK_SCALE = 18;
    public const REPORT_SCALE = 6;

    /** Сложение. */
    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::WORK_SCALE);
    }

    /** Вычитание. */
    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::WORK_SCALE);
    }

    /** Умножение. */
    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::WORK_SCALE);
    }

    /** Деление (a / b), b ≠ 0. */
    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::WORK_SCALE);
    }

    /**
     * Округление HALF_UP: 2.5 → 3, 2.499 → 2.
     *
     * Умножаем на 10^scale со слишком большим масштабом (WORK_SCALE),
     * берём целую часть (bcdiv scale=0) и остаток (bcmod scale=scale).
     * Если остаток ≥ 0.5 — округляем вверх.
     */
    public static function roundHalfUp(string $value, int $scale = self::REPORT_SCALE): string
    {
        if ($scale < 0) {
            $scale = 0;
        }
        $scalePow = (string) pow(10, $scale);
        // Умножаем со слишком большим масштабом — сохраняем все цифры
        $scaled = bcmul($value, $scalePow, self::WORK_SCALE);
        // Целая часть и дробная часть
        $intPart = (string) bcdiv($scaled, '1', 0);
        $fracPart = bcmod($scaled, '1', $scale);
        $half = bcdiv('1', '2', $scale);
        if (bccomp($fracPart, $half, $scale) >= 0) {
            return bcadd($intPart, '1', 0);
        }
        return $intPart;
    }

    /** Итоговая стоимость в формате отчёта: ровно 6 знаков. */
    public static function forReport(string $value): string
    {
        return self::roundHalfUp($value, self::REPORT_SCALE);
    }

    /** Сравнение. */
    public static function cmp(string $a, string $b): int
    {
        return bccomp($a, $b, self::WORK_SCALE);
    }
}
