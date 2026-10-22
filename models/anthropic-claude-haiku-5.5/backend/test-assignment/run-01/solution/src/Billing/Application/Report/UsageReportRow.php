<?php

declare(strict_types=1);

namespace App\Billing\Application\Report;

final readonly class UsageReportRow
{
    public function __construct(
        public string $user,
        public string $model,
        public int $tokensInput,
        public int $tokensOutput,
        public int $tokensCacheRead,
        public int $tokensCacheWrite,
        /** Сумма уже округлённых стоимостей событий, 6 знаков. */
        public string $costUsd,
    ) {
    }
}
