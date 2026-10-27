<?php

declare(strict_types=1);

namespace App\Application\Import;

/** Секция «providers[]». */
final readonly class PricesProvider
{
    public function __construct(
        public string $code,
        public string $name,
        public ?PricesOffPeak $offPeak,
    ) {
    }
}
