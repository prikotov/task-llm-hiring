<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

/**
 * Количество токенов одного вида. Целое ≥ 0 — отрицательных токенов не бывает,
 * поэтому инвариант проверяется в конструкторе, а не в бизнес-логике.
 */
final readonly class TokenCount implements \Stringable
{
    public function __construct(public int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException(sprintf('Количество токенов не может быть отрицательным: %d', $value));
        }
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
