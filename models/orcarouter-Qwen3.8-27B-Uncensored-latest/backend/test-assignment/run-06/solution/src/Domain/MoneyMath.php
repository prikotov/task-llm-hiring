<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика денег на bcmath (float запрещён).
 *
 * Все суммы в приложении храним в "микрос": целых долях 1e-6 USD
 * (стоимость события округляется до 6 знаков, поэтому представление
 * всегда точное).
 */
final class MoneyMath
{
    /** Рабочий масштаб промежуточных вычислений (>= 16 по заданию). */
    public const SCALE = 16;

    private const MICRO_PER_UNIT = 1000000;

    private function __construct()
    {
    }

    /** Сложение двух десятичных строк. */
    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    /** Перемножение десятичных строк. */
    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    /**
     * Округление стоимости события (>= 0) до 6 знаков HALF_UP.
     * Возвращает целое число микров юнитов (cost * 1e6).
     */
    public static function roundToMicros(string $costUsd): int
    {
        $shifted = bcmul($costUsd, (string) self::MICRO_PER_UNIT, 10);
        $whole = bcdiv($shifted, '1', 0); // trunc
        $fraction = bcsub($shifted, $whole, 2);

        if (bccomp($fraction, '0.5', 2) >= 0) {
            $whole = bcadd($whole, '1', 0);
        }

        return (int) $whole;
    }

    /** Форматирует целые микро юниты в строку с ровно 6 знаками после точки. */
    public static function formatMicros(int $micros): string
    {
        $sign = $micros < 0 ? '-' : '';
        $abs = abs($micros);

        return $sign
            . intdiv($abs, self::MICRO_PER_UNIT)
            . '.'
            . str_pad((string) ($abs % self::MICRO_PER_UNIT), 6, '0', STR_PAD_LEFT);
    }
}
