<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Immutable rate specification: either a unified "any" rate or
 * per-direction rates (input, output, cache_read, cache_write).
 */
final class RateSpec
{
    public function __construct(
        public readonly string $unit,          // 'per_1m_tokens' | 'per_1m_tokens_unified'
        public readonly string $currency,
        /** @var array<string,string> decimal-string rates, e.g. ['input' => '3.0', 'output' => '12.0'] */
        private readonly array $rates,
    ) {
        if ($unit === 'per_1m_tokens_unified') {
            if (!array_key_exists('any', $rates)) {
                throw new \InvalidArgumentException('Unified rate must define "any".');
            }
        } elseif ($unit === 'per_1m_tokens') {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $k) {
                if (!array_key_exists($k, $rates)) {
                    throw new \InvalidArgumentException("Split rate must define \"$k\".");
                }
            }
        } else {
            throw new \InvalidArgumentException("Unknown rate unit: $unit");
        }
    }

    public function rate(string $kind): string
    {
        if (!array_key_exists($kind, $this->rates)) {
            throw new \InvalidArgumentException("No rate for \"$kind\" (have: " . implode(', ', array_keys($this->rates)) . ')');
        }
        return $this->rates[$kind];
    }

    public function isUnified(): bool
    {
        return $this->unit === 'per_1m_tokens_unified';
    }

    /** @return array<string,string> */
    public function rates(): array
    {
        return $this->rates;
    }
}
