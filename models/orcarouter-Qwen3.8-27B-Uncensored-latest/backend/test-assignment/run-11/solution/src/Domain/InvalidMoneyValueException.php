<?php

declare(strict_types=1);

namespace App\Domain;

final class InvalidMoneyValueException extends \DomainException
{
    public static function new(string $value): self
    {
        return new self(sprintf('Invalid money value: %s', $value));
    }
}
