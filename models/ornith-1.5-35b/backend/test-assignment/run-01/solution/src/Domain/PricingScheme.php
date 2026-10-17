<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Схема тарификации: как ставка применяется к токенам.
 *
 * per_1m_tokens — ставка указана отдельно для каждого вида токенов,
 * стоимость = sum(tokens_вид * rate_вид) / 1_000_000.
 * per_1m_tokens_unified — единая ставка за любой токен,
 * стоимость = sum(tokens) * rate / 1_000_000.
 */
enum PricingScheme: string
{
    case PER_1M_TOKENS = 'per_1m_tokens';
    case PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * Есть ли схема единая (любой токен по одной ставке).
     */
    public function isUnified(): bool
    {
        return $this === self::PER_1M_TOKENS_UNIFIED;
    }
}
