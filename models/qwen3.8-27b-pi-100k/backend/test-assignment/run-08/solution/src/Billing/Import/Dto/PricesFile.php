<?php

declare(strict_types=1);

namespace App\Billing\Import\Dto;

/** Результат разбора файла истории цен (список вхождений). */
final class PricesFile
{
    /**
     * @param list<ProviderInput>  $providers
     * @param list<ModelInput>     $models
     * @param list<PriceRowInput>  $priceRows
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $priceRows,
    ) {
    }
}
