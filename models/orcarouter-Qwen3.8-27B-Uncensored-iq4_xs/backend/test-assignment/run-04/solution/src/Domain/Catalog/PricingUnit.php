<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

/**
 * Схема тарификации строки цен.
 */
enum PricingUnit: string
{
    /** Цена за 1 000 000 токенов отдельно для каждого вида. */
    case Per1MTokens = 'per_1m_tokens';

    /** Единая цена за 1 000 000 токенов любого вида. */
    case Per1MTokensUnified = 'per_1m_tokens_unified';

    /**
     * Набор ключей rates, ожидаемых для этой схемы.
     *
     * @return list<string>
     */
    public function expectedRateKeys(): array
    {
        return match ($this) {
            self::Per1MTokens => ['input', 'output', 'cache_read', 'cache_write'],
            self::Per1MTokensUnified => ['any'],
        };
    }
}
