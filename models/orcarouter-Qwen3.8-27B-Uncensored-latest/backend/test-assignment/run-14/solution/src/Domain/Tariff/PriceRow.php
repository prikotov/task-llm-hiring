<?php

declare(strict_types=1);

namespace App\Domain\Tariff;

/**
 * A single valid price row of the provider price book.
 *
 * Billing unit ("unit") determines the shape of "rates":
 *   - "per_1m_tokens"          keys: input, output, cache_read, cache_write
 *   - "per_1m_tokens_unified"  key:  any
 */
final class PriceRow
{
    private function __construct(
        public readonly int|string $id, // DB row id (int after persistence) or '' before
        public readonly string $provider,
        public readonly string $model,
        /** ISO-8601 UTC with "+00:00" suffix; lexicographically comparable. */
        public readonly string $validFrom,
        public readonly string $currency,
        public readonly string $unit,
        /** @var array<string, string> rate kind => rate (plain decimal per 1M tokens) */
        public readonly array $rates,
    ) {
    }

    /**
     * @param array{
     *   ?id: int|string,
     *   provider: string, model: string, validFrom: string,
     *   currency: string, unit: string,
     *   rates: array<string, string>,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        $unit = (string) ($data['unit'] ?? '');
        $rates = $data['rates'] ?? [];

        if ($unit === 'per_1m_tokens') {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
                if (!isset($rates[$kind]) || !is_string($rates[$kind]) || $rates[$kind] === '') {
                    throw new \ValueError(sprintf('Price row misses rate "%s" (unit per_1m_tokens).', $kind));
                }
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            if (!isset($rates['any']) || !is_string($rates['any']) || $rates['any'] === '') {
                throw new \ValueError('Price row misses rate "any" (unit per_1m_tokens_unified).');
            }
        } else {
            throw new \ValueError(sprintf('Unknown billing unit "%s".', $unit));
        }

        return new self(
            ($data['id'] ?? self::NO_ID),
            (string) $data['provider'],
            (string) $data['model'],
            (string) $data['validFrom'],
            (string) $data['currency'],
            $unit,
            $rates,
        );
    }

    public const NO_ID = 0;

    /**
     * Exact (unrounded) base cost for the given token counts.
     *
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     * @return string plain decimal
     */
    public function baseCost(array $tokens): string
    {
        $scale = \App\Shared\Money\Dec::SCALE;
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
        $total = $tokens['input'] + $tokens['output']
            + $tokens['cache_read'] + $tokens['cache_write'];

        return bcdiv(bcmul((string) $total, $this->rates['any'], $scale), '1000000', $scale);
    }
}
