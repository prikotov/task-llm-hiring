<?php

declare(strict_types=1);

namespace App\Application\Import;

/** Секция «models[]». */
final readonly class PricesModel
{
    public function __construct(
        public string $provider,
        public string $code,
    ) {
    }
}
