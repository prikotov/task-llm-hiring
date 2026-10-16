<?php

declare(strict_types=1);

namespace App\Ingest;

/**
 * Строка файла цен.
 *
 * @readonly
 */
final class PriceEntry
{
    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly string $unit,
        public readonly array $rates,
    ) {
    }
}
