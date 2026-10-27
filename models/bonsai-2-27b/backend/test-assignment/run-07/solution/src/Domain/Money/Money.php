<?php

declare(strict_types=1);

namespace App\Domain\Money;

/**
 * Деньги как точная строка bcmath.
 */
final class Money
{
    public function __construct(private readonly string $value)
    {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function add(self $other): self
    {
        return new self(App\Domain\Domain::add($this->value, $other->value));
    }

    public function sub(self $other): self
    {
        return new self(App\Domain\Domain::sub($this->value, $other->value));
    }
}
