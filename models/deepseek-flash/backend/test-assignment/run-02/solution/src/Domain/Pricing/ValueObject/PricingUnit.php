<?php

declare(strict_types=1);

namespace App\Domain\Pricing\ValueObject;

enum PricingUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    public function isSupportedTokenKind(string $kind): bool
    {
        return match ($this) {
            self::Per1mTokens => in_array($kind, ['input', 'output', 'cache_read', 'cache_write'], true),
            self::Per1mTokensUnified => $kind === 'any',
        };
    }
}
