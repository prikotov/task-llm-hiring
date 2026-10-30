<?php

declare(strict_types=1);

namespace App\Billing\Import\Dto;

use App\Billing\ValueObject\Price;
use DateTimeImmutable;

/** Валидированная строка цен из файла истории цен. */
final class PriceRowInput
{
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly Price $price,
    ) {
    }
}
