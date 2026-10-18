<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Схема тарификации (поле `unit` в истории цен).
 */
enum PriceUnit: string
{
    /** Цена за 1 000 000 токенов отдельно для каждого вида: input/output/cache_read/cache_write. */
    case Per1mTokens = 'per_1m_tokens';

    /** Единая цена за 1 000 000 токенов любого вида. */
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new \InvalidArgumentException(sprintf('Unknown price unit "%s"', $value));
    }
}
