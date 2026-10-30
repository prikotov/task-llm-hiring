<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Схема тарификации модели.
 *
 * @method static self PerToken()         per_1m_tokens
 * @method static self PerTokenUnified()  per_1m_tokens_unified
 */
enum PriceScheme: string
{
    /** Цена за 1M токенов отдельно для каждого вида. */
    case PerToken = 'per_1m_tokens';

    /** Единая цена за 1M токенов любого вида. */
    case PerTokenUnified = 'per_1m_tokens_unified';

    /** @return list<string> ожидаемые ключи ставок */
    public function rateKeys(): array
    {
        return match ($this) {
            self::PerToken => ['cache_read', 'cache_write', 'input', 'output'],
            self::PerTokenUnified => ['any'],
        };
    }
}
