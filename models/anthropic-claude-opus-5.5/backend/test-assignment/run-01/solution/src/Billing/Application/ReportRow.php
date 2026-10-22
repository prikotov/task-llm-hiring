<?php

declare(strict_types=1);

namespace App\Billing\Application;

final readonly class ReportRow
{
    public function __construct(
        public string $user,
        public string $model,
        public string $tokensInput,
        public string $tokensOutput,
        public string $tokensCacheRead,
        public string $tokensCacheWrite,
        public string $costUsd,
    ) {
    }
}
