<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Событие использования, валидированное к вставке.
 *
 * @readonly
 */
final class UsageEventRecord
{
    public function __construct(
        public readonly string $provider,
        public readonly string $requestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts,
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
    ) {
    }
}
