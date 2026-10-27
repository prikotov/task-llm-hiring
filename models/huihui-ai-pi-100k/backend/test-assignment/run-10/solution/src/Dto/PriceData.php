<?php

declare(strict_types=1);

namespace App\Dto;

use App\Domain\Billing\RateUnit;

/**
 * Строка цен из файла (проверена парсером).
 *
 * @param array<string,string> $rates ставки, decimal-строки
 */
final class PriceData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly RateUnit $unit,
        public readonly array $rates,
        public readonly string $currency,
    ) {
    }
}
