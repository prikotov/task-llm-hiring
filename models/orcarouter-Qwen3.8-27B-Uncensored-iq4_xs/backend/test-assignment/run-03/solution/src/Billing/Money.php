<?php

declare(strict_types=1);

namespace App\Billing;

/**
 * Точная десятичная арифметика для денег (bcmath).
 *
 * Тип float для денег запрещён. Все суммы — строки.
 */
final class Money
{
    /** Запас точности для промежуточных вычислений (>= 16 по заданию). */
    public const CALC_SCALE = 16;

    /** Кол-во знаков в итоговой стоимости события. */
    public const MONEY_SCALE = 6;

    /**
     * Округление до $scale знаков по правилу HALF_UP (0.5 → вверх).
     * Поддерживаются только неотрицательные суммы (стоимости).
     */
    public static function roundHalfUp(string $value, int $scale = self::MONEY_SCALE): string
    {
        $neg = str_starts_with($value, '-');
        $abs = $neg ? substr($value, 1) : ltrim($value, '+');

        // Добавляем 0.5 * 10^-scale и обрезаем (обрезание bcmath = floor для >= 0).
        $fracLen = $scale + 1;
        $halfUnit = '0.'.str_repeat('0', $fracLen - 1).'5';

        $sum = bcadd($abs, $halfUnit, $scale);
        $result = bcadd($sum, '0', $scale); // нормализация до ровно $scale мест

        return $neg && $result !== '0' && $result !== '0.'.str_repeat('0', $scale)
            ? '-'.$result
            : $result;
    }

    /**
     * Форматирует сумму ровно до 6 знаков после точки (без повторного округления).
     */
    public static function toSix(string $value): string
    {
        return bcadd($value, '0', self::MONEY_SCALE);
    }

    public static function zero(): string
    {
        return '0';
    }

    /**
     * Нормализует decimal-строку (убирает лишние пробелы/табы).
     */
    public static function clean(string $value): string
    {
        $value = trim($value);

        return $value === '' ? '0' : $value;
    }
}
