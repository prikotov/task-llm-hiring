<?php

declare(strict_types=1);

namespace App\Application\Catalog;

final class CatalogImportSummary
{
    public function __construct(
        public readonly int $providerRows,
        public readonly int $newProviders,
        public readonly int $modelRows,
        public readonly int $newModels,
        public readonly int $priceRows,
        public readonly int $newPriceRows,
    ) {
    }

    public function line(): string
    {
        return sprintf(
            '[prices:import] провайдеры: записей в файле %d, новых %d; модели: %d, новых %d; строки цен: %d, новых %d',
            $this->providerRows,
            $this->newProviders,
            $this->modelRows,
            $this->newModels,
            $this->priceRows,
            $this->newPriceRows,
        );
    }
}
