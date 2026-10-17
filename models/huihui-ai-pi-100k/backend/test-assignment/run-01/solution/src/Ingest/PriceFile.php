<?php

declare(strict_types=1);

namespace App\Ingest;

/**
 * Разобранный файл истории цен: провайдеры (по code), модели (порядком),
 * строки цен (списком).
 *
 * @readonly
 */
final class PriceFile
{
    /**
     * @param array<string, ProviderData> $providers
     * @param array<int, ModelRef>        $models
     * @param list<PriceEntry>            $prices
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }
}
