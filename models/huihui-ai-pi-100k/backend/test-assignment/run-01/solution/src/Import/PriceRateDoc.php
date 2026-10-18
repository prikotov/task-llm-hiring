<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\Unit;

/** Строка секции "prices" файла цен (до записи в БД). */
final readonly class PriceRateDoc
{
    /**
     * @param array<string, string> $rates ключи — input/output/cache_read/cache_write либо any
     */
    public function __construct(
        public string $provider,
        public string $model,
        public \DateTimeImmutable $validFrom,
        public string $currency,
        public Unit $unit,
        public array $rates,
    ) {
    }
}
