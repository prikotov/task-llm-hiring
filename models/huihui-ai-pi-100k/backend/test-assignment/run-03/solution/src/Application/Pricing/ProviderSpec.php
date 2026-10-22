<?php

declare(strict_types=1);

namespace App\Application\Pricing;

use App\Domain\Pricing\OffPeakWindow;

final readonly class ProviderSpec
{
    public function __construct(
        public string $code,
        public string $name,
        public ?OffPeakWindow $offPeak,
    ) {
    }
}
