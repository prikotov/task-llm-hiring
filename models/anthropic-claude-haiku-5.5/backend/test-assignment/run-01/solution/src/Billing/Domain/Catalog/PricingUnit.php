<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

enum PricingUnit: string
{
    /** Отдельная цена за 1M токенов для каждого вида. */
    case PER_1M_TOKENS = 'per_1m_tokens';

    /** Единая цена за 1M токенов любого вида. */
    case PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    /** @return list<string> ключи блока rates, обязательные для схемы */
    public function requiredRateKeys(): array
    {
        return match ($this) {
            self::PER_1M_TOKENS => ['input', 'output', 'cache_read', 'cache_write'],
            self::PER_1M_TOKENS_UNIFIED => ['any'],
        };
    }
}
