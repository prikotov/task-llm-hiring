<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Domain\Pricing\PriceUnit;
use DateTimeImmutable;

/**
 * Строка цен из входного файла (история цен / ретро-коррекция).
 *
 * @psalm-suppress UnusedProperty
 */
final readonly class PriceRowSpec
{
    /**
     * @param array{input?: null|string, output?: null|string, cache_read?: null|string, cache_write?: null|string, any?: null|string} $rates
     */
    public function __construct(
        public string $provider,
        public string $model,
        public DateTimeImmutable $validFrom,
        public string $currency,
        public PriceUnit $unit,
        public array $rates,
    ) {
    }
}
