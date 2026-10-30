<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

/**
 * Точная десятичная арифметика для денег на bcmath.
 *
 * Промежуточные значения ведутся с запасом точности (SCALE), чтобы ни одно
 * деление/умножение не теряло знаков; явное округление (HALF_UP) происходит
 * ровно один раз — при финализации стоимости события. Тип float для денег
 * нигде не используется.
 *
 * Все методы принимают и возвращают строки — decimal-литералы.
 */
final class Money
{
    /** Запас точности для всех промежуточных вычислений. */
    public const SCALE = 30;

    /** Итоговое количество знаков после точки у денежных величин. */
    public const MONEY_SCALE = 6;

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
        if (bccomp($b, '0', self::SCALE) === 0) {
            throw new ValueError('Деление на ноль недопустимо.');
        }

        return bcdiv($a, $b, self::SCALE);
    }

    public static function cmp(string $a, string $b): int
    {
        return bccomp($a, $b, self::SCALE);
    }

    /**
     * Округление HALF_UP (2.5 -> 3) до $scale знаков после точки.
     *
     * Реализация: floor(x * 10^scale + 0.5) / 10^scale. Для неотрицательных x
     * усечение (bcdiv с scale 0) эквивалентно floor. Стоимости в этой системе
     * всегда неотрицательны — отрицательное значение считается ошибкой.
     */
    public static function roundHalfUp(string $value, int $scale = self::MONEY_SCALE): string
    {
        if (bccomp($value, '0', self::SCALE) < 0) {
            throw new ValueError('HALF_UP-округление реализовано только для неотрицательных значений.');
        }

        $factor = bcpow('10', (string) $scale, 0);
        $int = bcdiv(bcadd(bcmul($value, $factor, self::SCALE), '0.5', self::SCALE), '1', 0);

        return bcdiv($int, $factor, $scale);
    }

    /** Форматирует значение с ровно $scale знаками после точки (например, '8.400000'). */
    public static function format(string $value, int $scale = self::MONEY_SCALE): string
    {
        return bcdiv($value, '1', $scale);
    }
}
