<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Строика тарифного плана: (provider, model, validFrom) — уникальный ключ.
 *
 * Унифицированная ставка: `any`.
 * Per-token: `input`, `output`, `cache_read`, `cache_write`.
 *
 * `rates` — map вида → rate (bcmath строка, напр. "3.000000").
 * `unit` — `per_1m_tokens` или `per_1m_tokens_unified`.
 */
final class PriceSheet
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_PER_TOKEN_UNIFIED = 'per_1m_tokens_unified';

    /** @param array<string,string> $rates */
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $unit,
        public readonly array $rates,
    ) {
        if ($unit === self::UNIT_PER_TOKEN) {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $k) {
                if (!isset($rates[$k])) {
                    throw new \InvalidArgumentException(sprintf('prices: missing rates.%s for per_1m_tokens', $k));
                }
            }
        } elseif ($unit === self::UNIT_PER_TOKEN_UNIFIED) {
            if (!isset($rates['any'])) {
                throw new \InvalidArgumentException('prices: missing rates.any for per_1m_tokens_unified');
            }
        } else {
            throw new \InvalidArgumentException(sprintf('prices: invalid unit "%s"', $unit));
        }
    }
}
