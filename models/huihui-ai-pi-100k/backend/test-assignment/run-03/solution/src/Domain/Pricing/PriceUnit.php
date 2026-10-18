<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

enum PriceUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    /**
     * @return list<TokenKind>
     */
    public function expectedRateKinds(): array
    {
        return match ($this) {
            self::Per1mTokens => [
                TokenKind::Input,
                TokenKind::Output,
                TokenKind::CacheRead,
                TokenKind::CacheWrite,
            ],
            self::Per1mTokensUnified => [TokenKind::Any],
        };
    }
}
