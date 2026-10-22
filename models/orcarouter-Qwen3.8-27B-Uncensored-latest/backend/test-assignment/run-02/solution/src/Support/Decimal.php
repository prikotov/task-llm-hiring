<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Точная десятичная арифметика на bcmath.
 *
 * Правило задания: все промежуточные вычисления — без округления (масштаб
 * >= 16), итоговая стоимость события округляется ровно один раз до 6 знаков
 * правилом HALF_UP. float для денег не используется.
 */
final class Decimal
{
    /** Рабочий масштаб промежуточных вычислений (>= 16 по требованиям). */
    public const SCALE = 20;

    /** Целевой масштаб итоговой стоимости (6 знаков). */
    public const MONEY_SCALE = 6;

    /**
     * Умножение двух десятичных величин с рабочим масштабом.
     */
    public static function mul(string $a, string $b): string
    {
        return bcmul(self::normalize($a), self::normalize($b), self::SCALE);
    }

    /**
     * Сложение двух десятичных величин с рабочим масштабом.
     */
    public static function add(string $a, string $b): string
    {
        return bcadd(self::normalize($a), self::normalize($b), self::SCALE);
    }

    /**
     * Деление на 1 000 000 (нормализация цены «за 1M токенов»).
     */
    public static function div1m(string $value): string
    {
        return bcdiv(self::normalize($value), '1000000', self::SCALE);
    }

    /**
     * Округление стоимости события до 6 знаков правилом HALF_UP.
     *
     * Для неотрицательных величин (стоимость всегда >= 0) это эквивалентно
     * «сдвинуть вправо на MONEY_SCALE, прибавить 0.5, отбросить хвост» —
     * без использования float.
     */
    public static function roundMoney(string $value): string
    {
        $normalized = self::normalize($value);
        $factor = bcpow('10', (string) self::MONEY_SCALE, 10);
        $shifted = bcadd(bcmul($normalized, $factor, self::SCALE), '0.5', self::SCALE);
        $integer = self::integerPart($shifted);
        return bcdiv($integer, $factor, self::MONEY_SCALE);
    }

    /**
     * Приведение суммы уже округлённых стоимостей к ровно 6 знакам
     * (без повторного округления слагаемых — только нормализация формата).
     */
    public static function formatMoney(string $value): string
    {
        return bcadd(self::normalize($value), '0', self::MONEY_SCALE);
    }

    public static function normalize(string $value): string
    {
        if (preg_match('/^-?\d+(\.\d+)?$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Некорректное десятичное значение: "%s"', $value));
        }
        return $value;
    }

    /** Целая часть неотрицательного десятичного числа (строкой). */
    private static function integerPart(string $d): string
    {
        $pos = ltrim($d, '-');
        $dot = strpos($pos, '.');
        $ip = $dot === false ? $pos : substr($pos, 0, $dot);
        return $ip === '' ? '0' : $ip;
    }
}
