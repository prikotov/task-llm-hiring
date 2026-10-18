<?php

declare(strict_types=1);

namespace App\Application\Catalog;

final class CatalogFile
{
    /**
     * @param list<ProviderSpec> $providers
     * @param list<ModelSpec>    $models
     * @param list<PriceSpec>    $prices
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }
}
