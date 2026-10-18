<?php

declare(strict_types=1);

namespace App\Application\Prices;

/**
 * Разобранная и проверенная история цен (входной файл).
 * Раздел `prices` обязателен; `providers`/`models` могут отсутствовать
 * (например, в файле ретро-коррекции).
 *
 * @psalm-suppress UnusedProperty
 */
final readonly class PriceFileSpec
{
    /**
     * @param ProviderSpec[] $providers
     * @param ModelSpec[] $models
     * @param PriceRowSpec[] $priceRows
     */
    public function __construct(
        public array $providers,
        public array $models,
        public array $priceRows,
    ) {
    }
}
