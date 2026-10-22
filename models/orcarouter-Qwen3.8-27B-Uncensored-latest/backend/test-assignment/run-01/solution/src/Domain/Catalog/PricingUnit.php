<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

enum PricingUnit: string
{
    case PerToken = 'per_1m_tokens';
    case PerUnified = 'per_1m_tokens_unified';

    /**
     * Названия видов токенов (ключи rates), требующиеся для данной схемы.
     *
     * @return list<string>
     */
    public function rateKeys(): array
    {
        return match ($this) {
            self::PerToken => ['input', 'output', 'cache_read', 'cache_write'],
            self::PerUnified => ['any'],
        };
    }
}
