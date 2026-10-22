<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\Pricing\RateCard;
use DateTimeImmutable;

final readonly class PriceDefinition
{
    public function __construct(
        public string $providerCode,
        public string $modelCode,
        public DateTimeImmutable $validFrom,
        public string $currency,
        public RateCard $rateCard,
    ) {
    }
}
