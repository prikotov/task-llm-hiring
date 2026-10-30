<?php

declare(strict_types=1);

namespace App\Domain;

/** Итог app:billing:reprice. */
final class RepriceResult
{
    public function __construct(
        public readonly int $eventsRepriced,
        /** Дельта (новая сумма − старая сумма) пересчитанных событий, ровно 6 знаков. */
        public readonly string $deltaUsd,
    ) {
    }
}
