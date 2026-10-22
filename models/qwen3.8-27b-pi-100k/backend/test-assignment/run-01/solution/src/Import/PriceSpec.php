<?php

declare(strict_types=1);

namespace App\Import;

use DateTimeImmutable;

/** Строка цены из файла справочника цен. */
final class PriceSpec
{
    /** @param array<string, string> $rates */
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly string $unit,
        public readonly array $rates,
    ) {
    }
}
