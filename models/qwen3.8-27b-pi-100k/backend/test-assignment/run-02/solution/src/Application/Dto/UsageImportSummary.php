<?php

declare(strict_types=1);

namespace App\Application\Dto;

/** Итог app:usage:import. */
final readonly class UsageImportSummary
{
    public function __construct(
        public int $inserted,
        public int $duplicates,
    ) {
    }
}
