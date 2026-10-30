<?php

declare(strict_types=1);

namespace App\Application;

final class RepriceResult
{
    public function __construct(
        public readonly int $ratedCount,
        public readonly int $unratedCount,
        public readonly int $errorCount = 0,
        /** @var string[] stderr-style lines for unrated events */
        public readonly array $stderrLines = [],
    ) {
    }
}
