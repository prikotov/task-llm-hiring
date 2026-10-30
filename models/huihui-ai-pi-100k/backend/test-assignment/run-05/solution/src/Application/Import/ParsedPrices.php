<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Разобранный файл справочника цен (providers, models, prices).
 * Секции могут быть пустыми — у reprice-файла это норма.
 *
 * @readonly
 */
final class ParsedPrices
{
    /**
     * @param ProviderRecord[] $providers
     * @param ModelRecord[]    $models
     * @param PriceRecord[]    $prices
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }
}
