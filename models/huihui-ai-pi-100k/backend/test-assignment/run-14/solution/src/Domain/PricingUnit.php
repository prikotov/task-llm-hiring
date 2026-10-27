<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Схема тарификации строки цен.
 */
enum PricingUnit: string
{
    /** Цена за 1 000 000 токенов отдельно по видам: input/output/cache_read/cache_write. */
    case PerToken = 'per_1m_tokens';
    /** Единая цена за 1 000 000 токенов любого вида. */
    case Unified = 'per_1m_tokens_unified';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? throw new \DomainException("Неизвестная схема тарификации: {$value}");
    }
}
