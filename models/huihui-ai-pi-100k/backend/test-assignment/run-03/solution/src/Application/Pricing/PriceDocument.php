<?php

declare(strict_types=1);

namespace App\Application\Pricing;

final readonly class PriceDocument
{
    /**
     * @param list<ProviderSpec> $providers
     * @param list<ModelSpec>   $models
     * @param list<PriceRowSpec> $priceRows
     */
    public function __construct(
        public array $providers,
        public array $models,
        public array $priceRows,
    ) {
    }
}
