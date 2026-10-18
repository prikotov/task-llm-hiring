<?php

declare(strict_types=1);

namespace App\Application\Usage;

final class UsageImportSummary
{
    public function __construct(
        public readonly int $received,
        public readonly int $inserted,
        public readonly int $duplicates,
    ) {
    }

    public function line(): string
    {
        return sprintf(
            '[usage:import] строк событий: %d, принято: %d, дубликатов пропущено: %d',
            $this->received,
            $this->inserted,
            $this->duplicates,
        );
    }
}
