<?php

declare(strict_types=1);

namespace App\Domain;

final class DivisionByZeroMoneyException extends \DomainException
{
    public static function new(): self
    {
        return new self('Money division by zero');
    }
}
