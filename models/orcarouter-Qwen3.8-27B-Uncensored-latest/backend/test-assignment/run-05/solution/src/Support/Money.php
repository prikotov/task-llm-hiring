<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Точная десятичная арифметика для денег (bcmath, float запрещён).
 *
 * Все промежуточные значения хранятся как строки со масштабом {@see self::SCALE}.
 * Единственное округление — итоговая стоимость события (см. {@see roundHalfUp()}).
 */
final class Money
{
    public const SCALE = 16;

    /** @param list<string> $summands */
    public static function add(string ...$summands): string
    {
        $result = '0';
        foreach ($summands as $summand) {
            $result = bcadd($result, $summand, self::SCALE);
        }

        return $result;
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
     * Округление HALF_UP (2.5 → 3) до $places знаков после точки.
     * Корректно для неотрицательных значений (стоимости событий).
     */
    public static function roundHalfUp(string $value, int $places): string
    {
        $factor = bcpow('10', (string) $places, 0);
        $scaled = bcadd(bcmul($value, $factor, self::SCALE), '0.5', self::SCALE);

        return bcdiv(bcdiv($scaled, '1', 0), $factor, $places);
    }

    /** Нормализация до ровно $places знаков после точки (без округления). */
    public static function format(string $value, int $places): string
    {
        return bcdiv($value, '1', $places);
    }
}
