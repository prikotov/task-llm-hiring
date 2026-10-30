<?php

declare(strict_types=1);

namespace App\Billing\Enum;

use App\Billing\Import\ImportException;

/**
 * Схема тарификации модели (unit в истории цен).
 */
enum PricingUnit: string
{
    /** Цена за 1 000 000 токенов отдельно для каждого вида: input, output, cache_read, cache_write. */
    case Per1MTokens = 'per_1m_tokens';

    /** Единая цена за 1 000 000 токенов любого вида (rate «any»). */
    case Per1MTokensUnified = 'per_1m_tokens_unified';

    public static function fromString(string $value): self
    {
        try {
            return self::from($value);
        } catch (\ValueError) {
            throw new ImportException(sprintf('Неизвестная схема тарификации "%s".', $value));
        }
    }

    /**
     * Какие ключи обязаны быть в rates для этой схемы.
     *
     * @return list<string>
     */
    public function requiredRateKeys(): array
    {
        return match ($this) {
            self::Per1MTokens => ['input', 'output', 'cache_read', 'cache_write'],
            self::Per1MTokensUnified => ['any'],
        };
    }
}
