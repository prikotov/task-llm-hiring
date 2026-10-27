<?php

declare(strict_types=1);

namespace App\Ingest\Defs;

use DateTimeImmutable;

/** Событие использования из JSONL-строки. */
final class UsageEventDef
{
    public function __construct(
        public readonly string $providerCode,
        public readonly string $providerRequestId,
        public readonly string $modelCode,
        public readonly string $user,
        public readonly DateTimeImmutable $tsUtc,
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
    ) {
    }
}
