<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Decimal;

/** Результат app:billing:reprice. */
final readonly class RepriceResult
{
    public function __construct(
        public int $events,
        public Decimal $delta,
    ) {
    }
}
