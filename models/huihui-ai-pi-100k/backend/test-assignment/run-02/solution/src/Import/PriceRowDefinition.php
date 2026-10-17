<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\PricingUnit;

/**
 * Строка цен из файла истории цен / ретро-коррекции.
 */
final class PriceRowDefinition
{
    /**
     * @param array<string, string> $rates ставки по видам токенов, десятичные строки
     */
    public function __construct(
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly PricingUnit $unit,
        public readonly array $rates,
    ) {
    }
}
