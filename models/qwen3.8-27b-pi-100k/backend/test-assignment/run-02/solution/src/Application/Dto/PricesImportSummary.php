<?php

declare(strict_types=1);

namespace App\Application\Dto;

/** Итог app:prices:import. */
final readonly class PricesImportSummary
{
    public function __construct(
        public EntityTally $providers,
        public EntityTally $models,
        public EntityTally $priceRows,
    ) {
    }
}
