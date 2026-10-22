<?php

declare(strict_types=1);

namespace App\Application\Import;

use DateTimeImmutable;

final readonly class UsageEventData
{
    public function __construct(
        public string $providerCode,
        public string $providerRequestId,
        public string $modelCode,
        public string $userId,
        public DateTimeImmutable $occurredAt,
        public int $tokensInput,
        public int $tokensOutput,
        public int $tokensCacheRead,
        public int $tokensCacheWrite,
    ) {
    }
}
