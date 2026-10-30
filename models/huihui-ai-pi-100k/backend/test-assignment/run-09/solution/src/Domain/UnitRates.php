<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Иммутабельное представление ставок строки цен (строки, не float).
 */
final class UnitRates
{
    /**
     * @param string      $unit               per_1m_tokens | per_1m_tokens_unified
     * @param string|null $input              — for per_1m_tokens
     * @param string|null $output             — for per_1m_tokens
     * @param string|null $cacheRead          — for per_1m_tokens
     * @param string|null $cacheWrite         — for per_1m_tokens
     * @param string|null $any                — for per_1m_tokens_unified
     */
    public function __construct(
        public readonly string $unit,
        public readonly ?string $input = null,
        public readonly ?string $output = null,
        public readonly ?string $cacheRead = null,
        public readonly ?string $cacheWrite = null,
        public readonly ?string $any = null,
    ) {
    }
}
