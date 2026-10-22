<?php

declare(strict_types=1);

namespace App\Domain\Money\Exception;

use InvalidArgumentException;

final class InvalidAmount extends InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(sprintf('Некорректное десятичное значение: "%s".', $value));
    }

    public static function divisionByZero(): self
    {
        return new self('Деление на ноль в денежном вычислении.');
    }
}
