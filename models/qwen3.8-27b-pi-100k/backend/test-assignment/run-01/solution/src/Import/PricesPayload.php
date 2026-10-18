<?php

declare(strict_types=1);

namespace App\Import;

/** Валидированное содержимое файла с ценами. */
final class PricesPayload
{
    /**
     * @param list<ProviderSpec> $providers
     * @param list<ModelSpec> $models
     * @param list<PriceSpec> $priceRows
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $priceRows,
    ) {
    }
}
