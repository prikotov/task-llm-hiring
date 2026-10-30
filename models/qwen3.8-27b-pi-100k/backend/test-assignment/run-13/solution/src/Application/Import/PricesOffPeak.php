<?php

declare(strict_types=1);

namespace App\Application\Import;

/** Секция «off_peak» (окно скидки и множитель). */
final readonly class PricesOffPeak
{
    public function __construct(
        public string $windowStart,
        public string $windowEnd,
        public string $multiplier,
    ) {
    }
}
