<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

use ValueError;

/**
 * A single valid price row of the provider price book.
 *
 * "unit" is one of:
 *   - "per_1m_tokens"          per-kind rates, keys: "input", "output",
 *                              "cache_read", "cache_write"
 *   - "per_1m_tokens_unified"  single rate, key "any"
 */
final class PriceRow
{
    private function __construct(
        public readonly string $provider,
        public readonly string $model,
        /** ISO-8601 UTC with "+00:00" suffix. */
        public readonly string $validFrom,
        public readonly string $currency,
        public readonly string $unit,
        /** @var array<string, string> rate kind => rate (plain decimal, per 1M tokens) */
        public readonly array $rates,
    ) {
    }

    /**
     * @param array{
     *   provider: string, model: string, validFrom: string,
     *   currency: string, unit: string, rates: array<string, string>,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        $unit = (string) ($data['unit'] ?? '');
        $rates = $data['rates'] ?? [];
        if ($unit === 'per_1m_tokens') {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
                if (!isset($rates[$kind]) || !is_string($rates[$kind]) || $rates[$kind] === '') {
                    throw new ValueError(sprintf('Price row misses rate "%s" (unit per_1m_tokens).', $kind));
                }
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            if (!isset($rates['any']) || !is_string($rates['any']) || $rates['any'] === '') {
                throw new ValueError('Price row misses rate "any" (unit per_1m_tokens_unified).');
            }
        } else {
            throw new ValueError(sprintf('Unknown billing unit "%s".', $unit));
        }

        return new self(
            (string) $data['provider'],
            (string) $data['model'],
            (string) $data['validFrom'],
            (string) $data['currency'],
            $unit,
            $rates
        );
    }

    /**
     * Exact (unrounded) base cost for the given token counts.
     *
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     * @return string plain decimal (no rounding)
     */
    public function baseCost(array $tokens): string
    {
        $scale = Dec::SCALE;
        if ($this->unit === 'per_1m_tokens') {
            $sum = bcadd(
                bcadd(
                    bcmul((string) $tokens['input'],      $this->rates['input'],      $scale),
                    bcmul((string) $tokens['output'],     $this->rates['output'],     $scale),
                    $scale
                ),
                bcadd(
                    bcmul((string) $tokens['cache_read'], $this->rates['cache_read'], $scale),
                    bcmul((string) $tokens['cache_write'],$this->rates['cache_write'],$scale),
                    $scale
                ),
                $scale
            );

            return bcdiv($sum, '1000000', $scale);
        }

        // per_1m_tokens_unified
        $total = $tokens['input'] + $tokens['output'] + $tokens['cache_read'] + $tokens['cache_write'];

        return bcdiv(bcmul((string) $total, $this->rates['any'], $scale), '1000000', $scale);
    }
}
