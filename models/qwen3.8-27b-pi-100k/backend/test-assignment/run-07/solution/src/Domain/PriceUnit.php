<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Схема тарификации модели:
 *  - per_1m_tokens        — цена за 1 000 000 токенов отдельно по каждому виду;
 *  - per_1m_tokens_unified — единая цена за 1 000 000 токенов любого вида.
 */
enum PriceUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    /**
     * Набор ключей ставок, обязательный для схемы (и только он).
     *
     * @return list<string>
     */
    public function requiredRateKeys(): array
    {
        return match ($this) {
            self::Per1mTokens => ['input', 'output', 'cache_read', 'cache_write'],
            self::Per1mTokensUnified => ['any'],
        };
    }
}
