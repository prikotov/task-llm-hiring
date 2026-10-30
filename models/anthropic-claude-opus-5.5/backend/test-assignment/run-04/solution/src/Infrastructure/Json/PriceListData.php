<?php

declare(strict_types=1);

namespace App\Infrastructure\Json;

/**
 * Разобранный файл истории цен.
 */
final readonly class PriceListData
{
    /**
     * @param list<array{code: string, name: string, off_peak: array{window_start: string, window_end: string, multiplier: string}|null}> $providers
     * @param list<array{provider: string, code: string}> $models
     * @param list<array{provider: string, model: string, valid_from: string, currency: string, unit: string, rates: array<string, string>}> $prices
     */
    public function __construct(
        public array $providers,
        public array $models,
        public array $prices,
    ) {
    }
}
