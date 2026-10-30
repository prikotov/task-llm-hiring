<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика для денег (bcmath), float запрещён.
 *
 * Все промежуточные значения — строки с запасом точности SCALE;
 * единственное допустимое округление — итоговая стоимость события
 * до 6 знаков по правилу HALF_UP («2.5» в последнем разряде → 3).
 */
final class Money
{
    /** Запас знаков для промежуточных вычислений (требование: ≥ 16). */
    public const SCALE = 24;

    /** 1 000 000 токенов — единица тарифа. */
    public const PER_1M = '1000000';

    private function __construct() {}

    public static function add(string $a, string $b): string
    {
        return \bcadd($a, $b, self::SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return \bcsub($a, $b, self::SCALE);
    }

    /** Стоимость «N токенов по цене rate за 1 000 000», без округления. */
    public static function tokensAtRate(int|string $tokens, string $rate): string
    {
        return \bcdiv(
            \bcmul((string) $tokens, $rate, self::SCALE),
            self::PER_1M,
            self::SCALE,
        );
    }

    /**
     * Округление до 6 знаков HALF_UP + нормализация представления
     * («8.4» → «8.400000»). Положительные и отрицательные значения
     * округляются от нуля (2.0000005 → 2.000001).
     */
    public static function toScale6(string $value): string
    {
        $neg = str_starts_with($value, '-');
        $abs = $neg ? substr($value, 1) : $value;

        $trunc = \bcdiv($abs, '1', 6);             // отбрасываем всё после 6-го знака
        $rest = \bcsub($abs, $trunc, 7);           // отброшенная доля
        if (\bccomp($rest, '0.0000005', 7) >= 0) { // HALF_UP: доля >= 0.5 последнего знака
            $trunc = \bcadd($trunc, '0.000001', 6);
        }
        $trunc = \bcdiv($trunc, '1', 6);           // ровно 6 знаков, напр. «14.262499»

        if ($neg && \bccomp($trunc, '0', 6) > 0) {
            return '-' . $trunc;
        }

        return $trunc;
    }
}
