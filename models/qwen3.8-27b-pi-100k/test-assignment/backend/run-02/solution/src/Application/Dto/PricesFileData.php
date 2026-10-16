<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Результат разбора файла цен (JSON) в целиком валидные строки.
 * Отсутствующие/пустые секции считаются пустыми списками — это позволяет
 * одному формату файла обслуживать и полный справочник, и коррекцию цен
 * (у которой секции providers/models могут отсутствовать).
 */
final class PricesFileData
{
    /**
     * @param list<ProviderData> $providers
     * @param list<ModelData>    $models
     * @param list<PriceData>    $prices
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }
}
