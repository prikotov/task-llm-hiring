<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\PricingUnit;

/** Строка цены из файла (проверенная запись справочника). */
final class PriceSpec
{
    /**
     * @param array<string, string> $rates ставки; ключи — по схеме $unit
     */
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly PricingUnit $unit,
        public readonly array $rates,
    ) {
    }
}
