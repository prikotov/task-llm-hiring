<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\Pricing\TokenUsage;
use DateTimeImmutable;

final readonly class UsageEventSpec
{
    public function __construct(
        public string $provider,
        public string $providerRequestId,
        public string $model,
        public string $user,
        public DateTimeImmutable $ts,
        public int $tokensInput,
        public int $tokensOutput,
        public int $tokensCacheRead,
        public int $tokensCacheWrite,
    ) {
    }

    public function tokenUsage(): TokenUsage
    {
        return new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite);
    }
}
