<?php

declare(strict_types=1);

namespace App\Application\Import;

/** Секция «prices[]» — одна строка цены. */
final readonly class PricesRowItem
{
    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        public string $provider,
        public string $model,
        public string $validFrom,
        public string $currency,
        public string $unit,
        public array $rates,
    ) {
    }
}
