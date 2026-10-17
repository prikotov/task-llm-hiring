<?php

declare(strict_types=1);

namespace App\Domain;

use function bccomp;
use function bcdiv;
use function bcmul;
use function bcsub;

/**
 * Точные десятичные операции для денег (bcmath), float запрещён.
 * Все суммы передаются/возвращаются строками.
 */
final class Decimal
{
    /** Запас точности для промежуточных вычислений (требование: ≥ 16). */
    public const CALC_SCALE = 20;

    /** Разрядность итоговой стоимости — 6 знаков. */
    public const MONEY_SCALE = 6;

    /**
     * HALF_UP-округление неотрицательного десятичного значения до 6 знаков.
     * (2.5 → 3 — для целых; в общем случае «половина — вверх от нуля».)
     */
    public static function roundHalfUp6(string $value): string
    {
        $scaled = bcmul($value, self::POWER10[6], self::CALC_SCALE); // value * 10^6

        $intPart = bcdiv($scaled, '1', 0); // floor (значение неотрицательное)
        $remainder = bcsub($scaled, $intPart, self::CALC_SCALE);

        if (bccomp($remainder, '0.5', 1) >= 0) {
            $intPart = bcadd($intPart, '1', 0);
        }

        return bcdiv($intPart, self::POWER10[6], self::MONEY_SCALE);
    }

    /** Сложение до 6 знаков (слагаемые уже округлены до 6 знаков). */
    public static function addMoney(string $a, string $b): string
    {
        return bcadd($a, $b, self::MONEY_SCALE);
    }

    /** Разность до 6 знаков, с знаком. */
    public static function subMoney(string $a, string $b): string
    {
        return bcsub($a, $b, self::MONEY_SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::CALC_SCALE);
    }

    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::CALC_SCALE);
    }

    /** Нормализует сумму к канонической форме с 6 знаками (например, 3 → 3.000000). */
    public static function canonicalMoney(string $value): string
    {
        return bcadd($value, '0', self::MONEY_SCALE);
    }

    private const POWER10 = [0 => '1', 1 => '10', 2 => '100', 3 => '1000', 4 => '10000', 5 => '100000', 6 => '1000000'];
}
