<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money\Decimal;

final readonly class RepriceResult
{
    public function __construct(
        public int $repricedEvents,
        public Decimal $delta,
    ) {
    }
}
