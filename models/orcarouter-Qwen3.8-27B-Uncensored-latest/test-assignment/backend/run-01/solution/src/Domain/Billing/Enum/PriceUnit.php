<?php

declare(strict_types=1);

namespace App\Domain\Billing\Enum;

enum PriceUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    public static function fromString(string $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        throw new \DomainException(sprintf('Неизвестная схема тарификации: "%s".', $value));
    }
}
