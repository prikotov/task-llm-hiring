<?php

declare(strict_types=1);

namespace App\Application\Pricing;

use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\TokenKind;
use DateTimeImmutable;

final readonly class PriceRowSpec
{
    /**
     * @param array<TokenKind, string> $rates
     */
    public function __construct(
        public string $providerCode,
        public string $modelCode,
        public DateTimeImmutable $validFrom,
        public string $currency,
        public PriceUnit $unit,
        public array $rates,
    ) {
    }
}
