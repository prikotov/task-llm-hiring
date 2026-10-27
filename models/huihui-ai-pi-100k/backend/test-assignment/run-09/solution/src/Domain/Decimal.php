<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Десятичная арифметика для денег на bcmath (float запрещён).
 *
 * Работает со строковыми десятичными числами; промежуточный масштаб
 * достаточно велик (16+), округление до 6 знаков — ровно одно, HALF_UP.
 */
final class Decimal
{
    /** Рабочий (неокончательный) масштаб вычислений. */
    public const SCALE = 16;

    /**
     * HALF_UP до 6 знаков (2.5 → 3). Значение должно быть неотрицательным
     * (стоимость события).
     */
    public static function halfUp6(string $value): string
    {
        $shifted = bcadd(ltrim($value, '-'), '0.0000005', 18);

        return bcdiv($shifted, '1', 6);
    }

    /**
     * Суммирование с точностью до 6 знаков (слагаемые уже округлены).
     */
    public static function sum6(string ...$values): string
    {
        $sum = '0';
        foreach ($values as $v) {
            $sum = bcadd($sum, $v, 6);
        }

        return $sum;
    }

    /**
     * Формат числа до 6 знаков ровно (знак сохраняется, например "-0.500000").
     */
    public static function format6(string $value): string
    {
        $value = trim($value);
        $sign = '';
        if (str_starts_with($value, '-')) {
            $sign = '-';
            $value = substr($value, 1);
        }
        if ($value === '' || $value === '.') {
            $value = '0';
        }
        $parts = array_pad(explode('.', $value, 2), 2, '');
        $frac = $parts[1];
        $need = 6 - strlen($frac);
        if ($need > 0) {
            $frac .= str_repeat('0', $need);
        } else {
            $frac = substr($frac, 0, 6);
        }

        return $sign . $parts[0] . '.' . $frac;
    }
}
