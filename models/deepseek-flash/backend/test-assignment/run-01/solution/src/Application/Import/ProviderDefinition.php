<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\Pricing\OffPeakWindow;

final readonly class ProviderDefinition
{
    public function __construct(
        public string $code,
        public string $name,
        public ?OffPeakWindow $offPeak,
    ) {
    }
}
