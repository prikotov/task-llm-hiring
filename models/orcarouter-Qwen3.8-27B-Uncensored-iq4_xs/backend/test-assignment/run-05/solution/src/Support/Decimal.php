<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Запасные десятичные строки: валидация и нормализация.
 */
final class Decimal
{
    private const RATE_PATTERN = '/^\d+(?:\.\d{1,6})?$/';

    private function __construct()
    {
    }

    /**
     * @param mixed $value
     *
     * @throws \DomainException
     */
    public static function validate(string $context, $value): string
    {
        if (!is_string($value) || !preg_match(self::RATE_PATTERN, $value)) {
            throw new \DomainException(sprintf('%s: ожидается десятичная строка (>= 0, до 6 знаков после точки)', $context));
        }

        return $value;
    }

    /** Нормализация к ровно 6 знакам после точки (без изменения значения). */
    public static function normalize6(string $value): string
    {
        return bcadd($value, '0', 6);
    }
}
