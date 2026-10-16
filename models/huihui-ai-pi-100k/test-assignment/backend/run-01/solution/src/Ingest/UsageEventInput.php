<?php

declare(strict_types=1);

namespace App\Ingest;

/**
 * Разобранное событие журнала использования.
 *
 * @readonly
 */
final class UsageEventInput
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $occurredAt,
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
    ) {
    }
}
