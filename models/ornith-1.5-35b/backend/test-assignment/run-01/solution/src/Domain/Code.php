<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Типизированная строка-код (провайдер, модель, пользователь, id запроса).
 *
 * Инвариант: непустая триммированная строка. Неявные преобразования
 * запрещены — всегда явно через fromString.
 */
final class Code
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new \InvalidArgumentException('Code cannot be empty');
        }
        $this->value = $trimmed;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
