<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Money;
use App\Shared\ImportException;

/**
 * A single pricing row for a model as it comes from the price catalog.
 *
 * Rates are exact decimal strings (per 1M tokens); arithmetic is done with
 * {@see Money} only.
 */
final class PriceEntry
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    /** @param array<string, string> $rates rate keys per scheme (see unit) */
    public function __construct(
        private readonly string $unit,
        private readonly array $rates,
    ) {
        if ($this->unit === self::UNIT_PER_TOKEN) {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $key) {
                if (!isset($rates[$key])) {
                    throw new ImportException(\sprintf('unit %s: missing rate "%s"', $this->unit, $key));
                }
            }
        } elseif ($this->unit === self::UNIT_UNIFIED) {
            if (!isset($rates['any'])) {
                throw new ImportException('unit per_1m_tokens_unified: missing rate "any"');
            }
        } else {
            throw new ImportException(\sprintf('unknown billing unit "%s"', $this->unit));
        }
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getRate(string $key): ?string
    {
        return $this->rates[$key] ?? null;
    }

    /**
     * Base cost of a usage record (before off-peak and final rounding).
     */
    public function baseCost(UsageRecord $usage): string
    {
        if ($this->unit === self::UNIT_UNIFIED) {
            return Money::div(
                Money::mul((string) $usage->totalTokens(), (string) $this->rates['any']),
                Money::ONE_MILLION_STR,
            );
        }

        $cost = '0';
        foreach ([
            'input' => $usage->tokensInput,
            'output' => $usage->tokensOutput,
            'cache_read' => $usage->tokensCacheRead,
            'cache_write' => $usage->tokensCacheWrite,
        ] as $key => $tokens) {
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens, (string) $this->rates[$key]), Money::ONE_MILLION_STR));
        }

        return $cost;
    }
}
