<?php

declare(strict_types=1);

namespace App\Import;

/**
 * Разрешённый файл истории цен (раздел может отсутствовать —
 * в файле ретро-коррекции providers/models допустимо не указывать).
 */
final class PriceFile
{
    /**
     * @param list<ProviderDefinition> $providers
     * @param list<ModelDefinition> $models
     * @param list<PriceRowDefinition> $prices
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }
}
