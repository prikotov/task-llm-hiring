<?php

declare(strict_types=1);

namespace App\Domain;

use App\ValueObject\Money;

/** Провайдер: код, название, оф-пik (необязательно). */
final class Provider
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeak $offPeak,
    ) {
    }
}
