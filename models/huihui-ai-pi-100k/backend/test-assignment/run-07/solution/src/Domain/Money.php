<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Деньги — только точная десятичная арифметика (bcmath), float запрещён.
 *
 * Все промежуточные вычисления выполняются с запасом точности (SCALE = 16 ≥ 6
 * знаков ставок + 10 запасных). ЕДИНСТВЕННОЕ округление — итоговой стоимости
 * события до 6 знаков, HALF_UP (2.5 -> 3 для последнего сохранённого знака).
 * Суммы отчёта складываются уже из округлённых стоимостей событий.
 */
final class Money
{
    /** Запас точности для промежуточных вычислений. */
    public const SCALE = 16;

    /** Число знаков итоговой стоимости (целых и дельт). */
    public const COST_SCALE = 6;

    private const HALF_MICRO = '0.0000005';

    private function __construct()
    {
    }

    public static function add(string $a, string $b): string
    {
        return \bcadd($a, $b, self::SCALE);
    }

    /** @return string Десятичная строка (без экспоненции) */
    public static function sub(string $a, string $b): string
    {
        return \bcsub($a, $b, self::SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return \bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        return \bcdiv($a, $b, self::SCALE);
    }

    /**
     * Округление стоимости НЕОТРИЦАТЕЛЬНОГО значения до 6 знаков, HALF_UP:
     * 14.26249875 -> 14.262499, 8.4 -> 8.400000, 0.0000005 -> 0.000001.
     */
    public static function halfUp6(string $value): string
    {
        // Добавление половины минимальной разрядности и отбрасывание хвоста
        // — ровно HALF_UP для неотрицательных чисел.
        $value = \bcdiv($value, '1', 30); // нормализация до 30 знаков

        return \bcdiv(\bcadd($value, self::HALF_MICRO, 30), '1', self::COST_SCALE);
    }

    /**
     * Форматирование ровно до 6 знаков после точки (отбрасывание хвоста
     * с запасом точности; для отрицательных — к нулю). Для сумм и дельт,
     * составляемых из значений, уже округлённых до 6 знаков.
     */
    public static function to6(string $value): string
    {
        $value = \bcdiv($value, '1', 30);

        return \bcdiv($value, '1', self::COST_SCALE);
    }
}
