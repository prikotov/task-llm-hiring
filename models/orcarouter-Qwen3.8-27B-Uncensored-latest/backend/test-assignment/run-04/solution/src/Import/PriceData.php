<?php

declare(strict_types=1);

namespace App\Import;

/** @readonly */
final class PriceData
{
    /** @param array<string, string> $rates decimal strings per scheme */
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly string $unit,
        public readonly array $rates,
    ) {
    }
}
