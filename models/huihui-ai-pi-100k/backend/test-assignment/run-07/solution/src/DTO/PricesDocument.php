<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Нормализованный и валидированный документ «история цен».
 *
 * @phpstan-type providerRow array{
 *     code: string,
 *     name: string,
 *     off_peak: array{window_start: string, window_end: string, multiplier: string}|null
 * }
 *
 * @phpstan-type modelRow  array{provider: string, code: string}
 *
 * @phpstan-type priceRow  array{
 *     provider: string,
 *     model: string,
 *     valid_from: \DateTimeImmutable,
 *     currency: string,
 *     unit: string,
 *     rates: array<string, string>
 * }
 */
final class PricesDocument
{
    /**
     * @param list<providerRow> $providers
     * @param list<modelRow> $models
     * @param list<priceRow> $prices
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }
}
