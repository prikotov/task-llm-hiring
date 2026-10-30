<?php

declare(strict_types=1);

namespace App\Domain\Common;

use function bcadd;
use function bcsub;
use function substr;

/**
 * Точная десятичная арифметика для денег (bcmath, масштаб ≥ 16).
 * Float для денежных величин не используется нигде в домене.
 */
final class Money
{
    /** Рабочий масштаб промежуточных вычислений (≥ 16 по требованиям). */
    public const SCALE = 20;

    /** Точность итоговой стоимости события и отчёта. */
    public const CENTS_PLACES = 6;

    public const MILLION = '1000000';

    /**
     * Окруglение по правилу HALF_UP (2.5 → 3 до нужных знаков).
     * Означенный единственный «округляющий» шаг при расчёте события.
     * Поддерживает неотрицательные и отрицательные значения.
     */
    public static function roundHalfUp(string $value, int $places = self::CENTS_PLACES): string
    {
        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : $value;

        $half = '0.'.str_repeat('0', $places).'5';
        $sum = bcadd($abs, $half, self::SCALE);

        $dot = strpos($sum, '.');
        if ($dot === false) {
            $intPart = $sum;
            $fracPart = '';
        } else {
            $intPart = substr($sum, 0, $dot);
            $fracPart = substr($sum, $dot + 1);
        }

        $result = $intPart.'.'.substr(str_pad($fracPart, $places, '0'), 0, $places);
        if ($result === '0.'.str_repeat('0', $places)) {
            $result = '0.'.str_repeat('0', $places); // «-0.000000» не доводим
            $negative = false;
        }

        return $negative ? '-'.$result : $result;
    }

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::SCALE);
    }

    /** Формат ровно с $places знаками после точки (без изменения значения). */
    public static function format(string $value, int $places = self::CENTS_PLACES): string
    {
        $negative = str_starts_with($value, '-');
        $abs = $negative ? substr($value, 1) : $value;

        $dot = strpos($abs, '.');
        if ($dot === false) {
            return ($negative ? '-' : '').$abs.'.'.str_repeat('0', $places);
        }

        $intPart = substr($abs, 0, $dot);
        $fracPart = substr($abs, $dot + 1);
        $fracPart = substr(str_pad($fracPart, $places, '0'), 0, $places);

        return ($negative ? '-' : '').$intPart.'.'.$fracPart;
    }
}
