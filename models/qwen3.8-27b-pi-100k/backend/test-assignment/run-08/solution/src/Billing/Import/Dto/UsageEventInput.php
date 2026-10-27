<?php

declare(strict_types=1);

namespace App\Billing\Import\Dto;

use App\Billing\ValueObject\TokenUsage;
use DateTimeImmutable;

/** Валидированное событие использования из JSONL. */
final class UsageEventInput
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly DateTimeImmutable $ts,
        public readonly TokenUsage $tokens,
    ) {
    }
}
