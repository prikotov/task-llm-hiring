<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Схема тарификации (unit в справочнике цен).
 */
enum RateUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    public static function fromCode(string $code): self
    {
        return self::tryFrom($code)
            ?? throw new \DomainException('Unknown rate unit: "' . $code . '"');
    }

    /**
     * Какой набор видов токенов участвует в расчёте.
     */
    public function rateKeys(): array
    {
        return match ($this) {
            self::Per1mTokens => ['input', 'output', 'cache_read', 'cache_write'],
            self::Per1mTokensUnified => ['any'],
        };
    }
}
