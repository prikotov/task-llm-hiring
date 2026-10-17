<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Exception\BillingException;

/**
 * Точная десятичная арифметика для денег (bcmath, без float).
 *
 * - все арифметические операции идут без округления (рабочий масштаб);
 * - единственное округление — roundHalfUp(): итоговая стоимость события
 *   до 6 знаков после точки, правило HALF_UP (2.5 → 3; 0.0000005 → 0.000001);
 * - format() только прицеливает точное значение под фиксированное число знаков.
 */
final class Money
{
    /** Масштаб итоговых сумм (стоимость события, отчёты). */
    public const MONEY_SCALE = 6;

    /** Рабочий масштаб промежуточных вычислений (>= 16 по заданию). */
    private const WORK_SCALE = 24;

    private function __construct()
    {
    }

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::WORK_SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::WORK_SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::WORK_SCALE);
    }

    public static function div(string $a, string $b): string
    {
        if (bccomp($b, '0', self::WORK_SCALE) === 0) {
            throw new BillingException('Деление на ноль в денежных вычислениях');
        }

        return bcdiv($a, $b, self::WORK_SCALE);
    }

    /**
     * Округление HALF_UP (половинки — от нуля; для неотрицательных чисел — вверх).
     *
     * Примеры: 0.0000004 -> 0.000000, 0.0000005 -> 0.000001,
     * 14.26249875 -> 14.262499, 11.000000 -> 11.000000.
     */
    public static function roundHalfUp(string $value, int $scale = self::MONEY_SCALE): string
    {
        if ($scale < 0) {
            throw new BillingException('Округлять до отрицательного масштаба нельзя');
        }

        $negative = str_starts_with($value, '-');
        $absolute = $negative ? substr($value, 1) : $value;

        // bcadd усечает сумму до заданного масштаба, поэтому прибавление
        // ровно половины последнего знака даёт корректное HALF_UP для
        // неотрицательных чисел:
        //   trunc6(v + 0.5e-6) = trunc6(v), если хвост v < 0.5e-6;
        //   trunc6(v + 0.5e-6) = trunc6(v) + 1e-6, если хвост v >= 0.5e-6.
        $half = '0.'.str_repeat('0', $scale).'5';
        $rounded = bcadd($absolute, $half, $scale);

        return $negative && bccomp($rounded, '0', $scale) > 0 ? '-'.$rounded : $rounded;
    }

    /**
     * Показание суммы ровно с $scale знаками после точки БЕЗ округления —
     * входное значение предполагается уже точным с точностью до нужного знака.
     */
    public static function format(string $value, int $scale = self::MONEY_SCALE): string
    {
        return bcadd($value, '0', $scale);
    }
}
