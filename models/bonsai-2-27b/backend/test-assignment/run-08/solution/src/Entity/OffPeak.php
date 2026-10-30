<?php

declare(strict_types=1);

namespace App\Entity;

final class OffPeak
{
    public function __construct(
        /** Часы:минуты, строка "HH:MM". */
        public readonly string $start,
        /** Часы:минуты, строка "HH:MM". */
        public readonly string $end,
        /** Множитель — десятичная строка. */
        public readonly string $multiplier
    ) {
    }
}
