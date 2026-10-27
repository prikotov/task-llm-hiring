<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\PriceUnit;

/**
 * Строка цены модели, действующая с момента validFrom (UTC, включительно).
 *
 * @readonly
 */
final class PriceSpec
{
    /**
     * @param array<string, string> $rates ставки — map «вид токена» => десятичная строка
     */
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly PriceUnit $unit,
        public readonly array $rates,
    ) {
    }
}
