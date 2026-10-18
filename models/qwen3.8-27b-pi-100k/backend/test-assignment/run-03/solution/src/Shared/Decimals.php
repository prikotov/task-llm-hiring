<?php

declare(strict_types=1);

namespace App\Shared;

/**
 * Валидация десятичных строк (цены, мультипликаторы).
 *
 * Деньги и цены в системе — только строки и bcmath; float не используется.
 */
final class Decimals
{
    /**
     * Проверяет, что строка — неотрицательное десятичное число
     * с не более чем $maxDecimals знаками после точки, и возвращает её как есть.
     */
    public static function validate(string $value, int $maxDecimals, string $context): string
    {
        $pattern = '/^\d{1,15}(\.\d{1,'.$maxDecimals.'})?$/D';
        if (preg_match($pattern, $value) !== 1) {
            throw new InputFormatException($context.': ожидается неотрицательное десятичное число '
                .'с не более чем '.$maxDecimals.' знаками после точки (в JSON — числовая строка, а не число)');
        }

        return $value;
    }
}
