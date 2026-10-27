<?php

declare(strict_types=1);

namespace App\Ingest\Defs;

use DateTimeImmutable;

/** Строка цены из файла-каталога. */
final class PriceDef
{
    /** @param array{input: string, output: string, cache_read: string, cache_write: string}|array{any: string} $rates */
    public function __construct(
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly DateTimeImmutable $validFromUtc,
        public readonly string $currency,
        public readonly string $unit,
        public readonly array $rates,
    ) {
    }
}
