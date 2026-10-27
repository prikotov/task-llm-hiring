<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Immutable snapshot of the price book: providers, models, all price rows.
 *
 * Row selection by (provider, model, tsUtc) returns the row whose validFrom
 * is the greatest value ≤ tsUtc (most recent in force); null ⇒ no price row
 * in force at that time ⇒ the event is unrated, not an error.
 */
final class PriceBook
{
    /**
     * @param ProviderRef[] $providers        code => ref
     * @param PriceRow[]    $allRows          flat list, any order
     * @param string[]      $knownModelCodes  model codes that exist (may be unknown to the book if the provider is known)
     */
    public function __construct(
        private readonly array $providers,
        /** @var array<array-key, PriceRow> */
        private readonly array $allRows,
        /** @var set<string>|array<string,bool> known "<provider>:<model>" */
        private readonly array $knownModelKeys,
    ) {
    }

    public static function fromRows(array $providers, array $allRows, array $knownModelKeys): self
    {
        return new self($providers, $allRows, $knownModelKeys);
    }

    public function knownModels(): array
    {
        return array_keys($this->knownModelKeys);
    }

    public function hasModel(string $providerCode, string $modelCode): bool
    {
        return isset($this->knownModelKeys[$providerCode.':'.$modelCode]);
    }

    public function providerOffPeak(string $providerCode): ?OffPeakWindow
    {
        $ref = isset($this->providers[$providerCode]) ? $this->providers[$providerCode] : null;

        return $ref instanceof ProviderRef ? $ref->offPeak : null;
    }

    public function allRows(): array
    {
        return array_values($this->allRows);
    }

    /**
     * Most recent price row (validFrom ≤ tsUtc) for the given model.
     */
    public function rateInForce(string $providerCode, string $modelCode, \DateTimeImmutable $tsUtc): ?PriceRow
    {
        $chosen = null;
        foreach ($this->allRows as $row) {
            if ($row->providerCode !== $providerCode || $row->modelCode !== $modelCode) {
                continue;
            }
            if ($row->validFrom <= $tsUtc && ($chosen === null || $row->validFrom >= $chosen->validFrom)) {
                $chosen = $row;
            }
        }

        return $chosen;
    }
}
