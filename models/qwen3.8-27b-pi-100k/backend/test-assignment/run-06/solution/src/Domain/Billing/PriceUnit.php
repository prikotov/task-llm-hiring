<?php

declare(strict_types=1);

namespace App\Domain\Billing;

enum PriceUnit: string
{
    case Per1MToken = 'per_1m_tokens';
    case Per1MTokenUnified = 'per_1m_tokens_unified';

    public static function fromString(string $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown pricing unit "%s".', $value));
    }
}
