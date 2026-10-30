<?php

declare(strict_types=1);

namespace App\Application\Billing;

final readonly class RepriceResult
{
    public function __construct(
        public int $events,
        public string $deltaUsd,
    ) {
    }
}
