<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Проверка десятичных строк (bcmath-совместимые, неотрицательные).
 */
final class Decimals
{
    public static function assert(string $value, string $what): string
    {
        if (preg_match('/^\d+(\.\d+)?$/', $value) !== 1) {
            throw ImportInputException::field($what, 'must be a non-negative decimal string, got '.var_export($value, true));
        }

        return $value;
    }
}
