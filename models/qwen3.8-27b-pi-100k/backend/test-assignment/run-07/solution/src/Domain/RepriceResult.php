<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Результат app:billing:reprice: сколько событий пересчитано
 * и дельта стоимости (новая − старая) в 6 знаках, со знаком.
 *
 * @readonly
 */
final class RepriceResult
{
    public function __construct(
        public readonly int $repricedEvents,
        public readonly string $deltaUsd,
    ) {
    }
}
