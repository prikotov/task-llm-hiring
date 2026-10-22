<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Immutable projection of a usage event sufficient for rating.
 */
final class UsageRecord
{
    public function __construct(
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
        public readonly \DateTimeImmutable $ts,
    ) {
    }

    public function totalTokens(): int
    {
        return $this->tokensInput + $this->tokensOutput + $this->tokensCacheRead + $this->tokensCacheWrite;
    }
}
