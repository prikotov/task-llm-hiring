<?php

declare(strict_types=1);

namespace App\Application\Dto;

/** Итог app:billing:reprice. */
final readonly class RepriceSummary
{
    public function __construct(
        public int $eventsRepriced,
        public string $deltaUsd,
    ) {
    }
}
