<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Схема тарификации.
 *
 * PerToken — отдельная цена за 1 000 000 токенов для каждого вида (input, output,
 * cache_read, cache_write); Unified — единая цена за 1 000 000 токенов любого вида.
 */
enum BillingUnit: string
{
    case PerToken = 'per_1m_tokens';
    case Unified = 'per_1m_tokens_unified';

    /**
     * Ключи ставок, которые должны присутствовать в rates при данной схеме.
     *
     * @return list<string>
     */
    public function rateKeys(): array
    {
        return match ($this) {
            self::PerToken => ['input', 'output', 'cache_read', 'cache_write'],
            self::Unified => ['any'],
        };
    }
}
