<?php

declare(strict_types=1);

namespace App\Domain;

final class Unit
{
    /** Цена за 1 000 000 токенов отдельно для каждого вида токенов. */
    public const PER_1M_TOKENS = 'per_1m_tokens';
    /** Единая цена за 1 000 000 токенов любого вида. */
    public const PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    private const RATES_KEYS = [
        self::PER_1M_TOKENS => ['input', 'output', 'cache_read', 'cache_write'],
        self::PER_1M_TOKENS_UNIFIED => ['any'],
    ];

    public static function isValid(string $unit): bool
    {
        return isset(self::RATES_KEYS[$unit]);
    }

    public static function rateKeys(string $unit): array
    {
        return self::RATES_KEYS[$unit];
    }
}
