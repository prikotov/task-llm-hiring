<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Документ истории цен (результат парсинга).
 *
 * @param PricesProvider[] $providers
 * @param PricesModel[]    $models
 * @param PricesRowItem[]  $prices
 */
final readonly class PricesDocument
{
    public function __construct(
        public array $providers,
        public array $models,
        public array $prices,
    ) {
    }
}
