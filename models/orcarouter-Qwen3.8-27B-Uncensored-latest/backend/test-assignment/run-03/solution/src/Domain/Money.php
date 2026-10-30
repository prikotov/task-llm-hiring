<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика денег на базе bcmath.
 *
 * Для денег запрещён float — все вычисления ведутся строковыми
 * десятичными числами. Заносимый масштаб (Scale) — промежуточная
 * точность, достаточная, чтобы не потерять хвост при делениях и умножениях.
 */
final class Money
{
    /**
     * Рабочий масштаб промежуточных вычислений (>= 16 по ТЗ).
     * Большее значение не влияет на корректность, лишь на запас точности.
     */
    private const SCALE = 24;

    private function __construct()
    {
    }

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

    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::SCALE);
    }

    /**
     * Округление по правилу HALF_UP (2.5 -> 3; 14.26249875@6 -> 14.262499)
     * до $dp знаков после точки. Поддерживает неотрицательные значения,
     * которых в данной предметной области достаточно.
     */
    public static function roundHalfUp(string $value, int $dp = 6): string
    {
        if ($dp < 0) {
            throw new \InvalidArgumentException('dp must be >= 0');
        }

        $factor = '1' . str_repeat('0', $dp);

        // Переводим в "целые доли" без/с одним лишним знаком, чтобы решить
        // по правилу HALF_UP только на основании последней цифры.
        $intPart = bcmul($value, $factor, 0);   // округление вниз (value >= 0)
        $withTail = bcmul($value, $factor, 1);  // + одна десятичная цифра хвоста

        $frac = bcsub($withTail, $intPart, 1);  // в диапазоне [0; 1)
        $parts = explode('.', $frac);
        $tenthsDigit = $parts[1][0] ?? '0';

        if ($tenthsDigit >= '5') {
            $intPart = bcadd($intPart, '1', 0);
        }

        return bcdiv($intPart, $factor, $dp);
    }

    /**
     * Приводит значение к строке с ровно $dp знаками после точки
     * (без пересчёта цифр, только формат), что используется в отчётах
     * и на выходе сумм-округлений событий.
     */
    public static function format(string $value, int $dp = 6): string
    {
        return bcadd($value, '0', $dp);
    }
}
