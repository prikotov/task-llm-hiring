<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Неизменяемое «снимок» данных, достаточный для тарификации одного события:
 * выбранный прайс-лист с множителями и (опционально) оф-пик провайдера.
 */
final readonly class PriceSheet
{
    public function __construct(
        public string $currency,
        public string $unit,
        /** @var array<string,Money> ключи: input/output/cache_read/cache_write или any */
        public array $rates,
    ) {
    }

    public function rate(string $kind): Money
    {
        return $this->rates[$kind];
    }
}
