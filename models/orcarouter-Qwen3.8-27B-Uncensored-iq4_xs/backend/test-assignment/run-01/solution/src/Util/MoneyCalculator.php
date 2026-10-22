<?php

declare(strict_types=1);

namespace App\Util;

/**
 * Точные десятичные вычисления для денег на bcmath.
 *
 * float для денег не используется: все значения — строки, все операции —
 * bcmath-функции. Промежуточные вычисления — с запасным масштабом
 * CALC_SCALE (без округления на промежуточных этапах); итоговое
 * округление стоимости события (HALF_UP, 6 знаков) — в roundHalfUp().
 */
final class MoneyCalculator
{
    /** Масштаб промежуточных вычислений (запас точности). */
    public const CALC_SCALE = 20;

    /** Кол-во знаков окончательной стоимости денег. */
    public const MONEY_SCALE = 6;

    /** Цена задана за 1 000 000 токенов. */
    public const UNITS_PER_RATE = 1_000_000;

    private const DECIMAL_PATTERN = '/^\d+(\.\d+)?$/';

    private function __construct()
    {
    }

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::CALC_SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::MONEY_SCALE);
    }

    /**
     * Точное сравнение десятчных строк по значению (без учёта хвостовых нулей).
     */
    public static function eq(string $a, string $b): bool
    {
        return bccomp($a, $b, self::CALC_SCALE) === 0;
    }

    public static function multiply(string $a, string $b): string
    {
        return bcmul($a, $b, self::CALC_SCALE);
    }

    public static function divide(string $a, string $b): string
    {
        return bcdiv($a, $b, self::CALC_SCALE);
    }

    /**
     * Проверка десятичной строки: неотрицательное число.
     */
    public static function assertDecimal(string $value): string
    {
        if (preg_match(self::DECIMAL_PATTERN, $value) !== 1) {
            throw new \DomainException(sprintf(
                'Ожидалась десятичная строка (неотрицательное число), получено: %s',
                $value,
            ));
        }

        return $value;
    }

    /**
     * Округление до $scale знаков, HALF_UP (0.5 — в сторону от нуля).
     * Вход: строка с любым количеством знаков; выход: ровно $scale знаков.
     */
    public static function roundHalfUp(string $value, int $scale = self::MONEY_SCALE): string
    {
        self::assertDecimal(ltrim($value, '-'));

        $negative = str_starts_with($value, '-');
        $abs = ltrim($negative ? substr($value, 1) : $value, '0');
        if ($abs === '') {
            $abs = '0';
        }

        $factor = 10 ** $scale;
        // HALF_UP: прибавляем 0.5×10^-scale до отбрасывания дробной части
        // переноса запятой (bc* с масштабом 0 отбрасывает хвост).
        $half = '0.' . str_repeat('0', $scale) . '5';
        $scaled = (int) bcmul(bcadd($abs, $half, self::CALC_SCALE), (string) $factor, 0);

        $intPart = intdiv($scaled, $factor);
        $fracPart = str_pad((string) ($scaled % $factor), $scale, '0', STR_PAD_LEFT);

        $result = $intPart . '.' . $fracPart;

        return ($negative && $result !== '0.' . str_repeat('0', $scale)) ? '-'.$result : $result;
    }

    /**
     * Приведение к канонической десятичной форме: ровно $scale знаков
     * после точки, без изменения величины (вход уже с ≤ $scale знаков).
     */
    public static function normalize(string $value, int $scale = self::MONEY_SCALE): string
    {
        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : $value;

        $dotPos = strpos($abs, '.');
        $intPart = $dotPos === false ? $abs : substr($abs, 0, $dotPos);
        $fracPart = $dotPos === false ? '' : substr($abs, $dotPos + 1);

        $intPart = ltrim($intPart, '0') ?: '0';
        $fracPart = str_pad(substr($fracPart, 0, $scale), $scale, '0');

        $result = $intPart . '.' . $fracPart;

        return ($negative && $intPart !== '0') ? '-'.$result : $result;
    }
}
