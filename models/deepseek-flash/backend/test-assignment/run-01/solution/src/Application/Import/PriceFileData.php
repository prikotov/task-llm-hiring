<?php

declare(strict_types=1);

namespace App\Application\Import;

final readonly class PriceFileData
{
    /**
     * @param list<ProviderDefinition> $providers
     * @param list<ModelDefinition> $models
     * @param list<PriceDefinition> $prices
     */
    public function __construct(
        public array $providers,
        public array $models,
        public array $prices,
    ) {
    }
}
