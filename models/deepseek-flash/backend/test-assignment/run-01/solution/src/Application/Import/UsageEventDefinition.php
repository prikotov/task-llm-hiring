<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\Usage\TokenUsage;
use DateTimeImmutable;

final readonly class UsageEventDefinition
{
    public function __construct(
        public string $provider,
        public string $providerRequestId,
        public string $model,
        public string $user,
        public DateTimeImmutable $occurredAt,
        public TokenUsage $usage,
    ) {
    }
}
