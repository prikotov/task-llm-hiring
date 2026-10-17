<?php

declare(strict_types=1);

namespace App\Domain;

use function bcadd;
use function bcdiv;
use function bcpow;
use function bcmul;
use function bcsub;

/**
 * Точная десятичная арифметика на bcmath для денежных величин.
 *
 * Тип float для денег запрещён: все суммы/множители — строки.
 * Промежуточные вычисления ведутся с масштабом 20 знаков (запас точности),
 * окончательное округление — один раз, через roundHalfUp().
 * Формат результата: строка вида "123.456789".
 */
final class Decimal
{
    private const SCALE = 20;

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
     * Округление до $digits знаков после точки, правило HALF_UP
     * (от нуля: 2.5 -> 3, -2.5 -> -3).
     */
    public static function roundHalfUp(string $value, int $digits = 6): string
    {
        if ($digits < 0) {
            throw new \InvalidArgumentException('Decimal::roundHalfUp: digits must be >= 0');
        }

        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : $value;
        $factor = bcpow('10', (string) $digits, self::SCALE + 20);
        $big = self::SCALE + 20;

        // |v| >= 0, поэтому bcdiv(..., 0) = floor: floor(|v| * 10^digits + 0.5)
        $halfRounded = bcadd(bcmul($abs, $factor, $big), '0.5', $big);
        $integer = bcdiv($halfRounded, '1', 0);

        $result = bcdiv($integer, $factor, $digits);

        return ($negative && ltrim($result, '0.') !== '') ? '-'.$result : $result;
    }

    /**
     * Приводит строку к строгому формату с ровно $digits знаками
     * после точки (без изменения значения, если знаков не больше).
     */
    public static function format(string $value, int $digits = 6): string
    {
        return bcdiv(self::roundHalfUp(rtrim($value, " \t\n"), $digits), '1', $digits);
    }
}
