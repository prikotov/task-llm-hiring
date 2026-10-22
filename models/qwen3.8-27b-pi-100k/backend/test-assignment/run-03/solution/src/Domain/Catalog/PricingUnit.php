<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Shared\InputFormatException;

/**
 * Схема тарификации строки цен.
 *
 * per_1m_tokens          — цена за 1 000 000 токенов отдельно для каждого вида;
 * per_1m_tokens_unified  — единая цена за 1 000 000 токенов любого вида.
 */
enum PricingUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    /** Ключи ставок, обязанные присутствовать в rates для этой схемы. */
    public function rateKeys(): array
    {
        return match ($this) {
            self::Per1mTokens => ['input', 'output', 'cache_read', 'cache_write'],
            self::Per1mTokensUnified => ['any'],
        };
    }
}
