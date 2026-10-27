<?php

declare(strict_types=1);

namespace App\Application\Ingestion;

use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\StoredPriceRow;

/**
 * Разобранное содержимое файла цен (доменное представление).
 *
 * @readonly
 */
final class PriceCatalogInput
{
    /**
     * @param array<string, array{name: string, off_peak: ?\App\Domain\Pricing\OffPeakPolicy}> $providers
     * @param array<int, array{provider: string, code: string}> $models
     * @param array<int, StoredPriceRow> $priceRows готовые Doctrine-сущности для persistence
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $priceRows,
    ) {
    }
}
