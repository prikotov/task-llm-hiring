<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

enum RateUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    /** @return list<string> требуемые ключи rates для схемы */
    public function rateKeys(): array
    {
        return match ($this) {
            self::Per1mTokens => ['input', 'output', 'cache_read', 'cache_write'],
            self::Per1mTokensUnified => ['any'],
        };
    }
}
