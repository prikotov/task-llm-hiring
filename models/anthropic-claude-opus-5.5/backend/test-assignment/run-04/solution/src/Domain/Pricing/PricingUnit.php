<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Схема тарификации провайдера.
 */
enum PricingUnit: string
{
    /** Цена за 1 000 000 токенов отдельно для каждого вида токенов. */
    case Per1MTokens = 'per_1m_tokens';

    /** Единая цена за 1 000 000 токенов любого вида. */
    case Per1MTokensUnified = 'per_1m_tokens_unified';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new \InvalidArgumentException(sprintf('Неизвестная схема тарификации: "%s".', $value));
    }

    /**
     * Виды токенов, для которых должна быть задана ставка.
     *
     * @return list<string>
     */
    public function requiredRateKeys(): array
    {
        return match ($this) {
            self::Per1MTokens => [TokenKind::Input->value, TokenKind::Output->value, TokenKind::CacheRead->value, TokenKind::CacheWrite->value],
            self::Per1MTokensUnified => ['any'],
        };
    }
}
