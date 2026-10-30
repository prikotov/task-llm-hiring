<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * A price line for a model: billing unit + rates, effective from $validFrom.
 *
 * Units:
 *  - per_1m_tokens:        rates for input/output/cache_read/cache_write
 *  - per_1m_tokens_unified: single rate for any token kind
 *
 * Rates are exact decimal strings (bcmath), 6 significant places per spec.
 */
final class Price
{
    public const UNIT_PER_1M = 'per_1m_tokens';
    public const UNIT_PER_1M_UNIFIED = 'per_1m_tokens_unified';

    /** @param array<string,string> $rates */
    public function __construct(
        private readonly string $unit,
        private readonly string $validFromIso,
        private readonly string $currency,
        private readonly array $rates,
    ) {
    }

    public static function fromPayload(array $payload): self
    {
        $unit = (string) ($payload['unit'] ?? '');
        if (!in_array($unit, [self::UNIT_PER_1M, self::UNIT_PER_1M_UNIFIED], true)) {
            throw new \InvalidArgumentException("Unknown billing unit: {$unit}");
        }
        $rates = $payload['rates'] ?? [];
        if (!is_array($rates) || $rates === []) {
            throw new \InvalidArgumentException('Price requires a non-empty "rates" object');
        }
        foreach ($rates as $k => $v) {
            if (!is_string($v) && !is_int($v)) {
                throw new \InvalidArgumentException("rate '{$k}' must be a decimal string");
            }
            $rates[$k] = (string) $v;
        }
        if ($unit === self::UNIT_PER_1M) {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $k) {
                if (!array_key_exists($k, $rates)) {
                    throw new \InvalidArgumentException("per_1m_tokens price requires rate '{$k}'");
                }
            }
        } else {
            if (!array_key_exists('any', $rates)) {
                throw new \InvalidArgumentException('per_1m_tokens_unified price requires rate "any"');
            }
        }
        return new self(
            $unit,
            (string) ($payload['valid_from'] ?? ''),
            (string) ($payload['currency'] ?? 'USD'),
            $rates,
        );
    }

    public function unit(): string
    {
        return $this->unit;
    }

    public function validFromIso(): string
    {
        return $this->validFromIso;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function rate(string $kind): string
    {
        if (!array_key_exists($kind, $this->rates)) {
            throw new \OutOfBoundsException("rate '{$kind}' not defined for this line");
        }
        return $this->rates[$kind];
    }

    /**
     * Returns the exact (unrounded) cost for the given token counts.
     * All arithmetic is bcmath at scale 20.
     */
    public function costFor(int $tokensIn, int $tokensOut, int $tokensCacheRead, int $tokensCacheWrite): Money
    {
        if ($this->unit === self::UNIT_PER_1M_UNIFIED) {
            $total = $tokensIn + $tokensOut + $tokensCacheRead + $tokensCacheWrite;
            $num = bcmul((string) $total, $this->rates['any'], 10);
            return Money::from(self::divideBy1M($num));
        }
        $cIn     = self::part((string) $tokensIn,     $this->rates['input']);
        $cOut    = self::part((string) $tokensOut,    $this->rates['output']);
        $cRd     = self::part((string) $tokensCacheRead, $this->rates['cache_read']);
        $cWr     = self::part((string) $tokensCacheWrite, $this->rates['cache_write']);
        $sum = bcadd(bcadd(bcadd($cIn, $cOut, 10), $cRd, 10), $cWr, 10);
        return Money::from($sum);
    }

    private static function part(string $tokens, string $rate): string
    {
        return self::divideBy1M(bcmul($tokens, $rate, 10));
    }

    private static function divideBy1M(string $n): string
    {
        return bcdiv($n, '1000000', 20);
    }

    /** @return array<string,string> */
    public function serialize(): array
    {
        return [
            'unit' => $this->unit,
            'valid_from' => $this->validFromIso,
            'currency' => $this->currency,
            'rates' => $this->rates,
        ];
    }

    /** @param array<string,mixed> $data */
    public static function deserialize(array $data): self
    {
        return new self(
            (string) ($data['unit'] ?? ''),
            (string) ($data['valid_from'] ?? ''),
            (string) ($data['currency'] ?? 'USD'),
            array_map('strval', (array) ($data['rates'] ?? [])),
        );
    }
}
