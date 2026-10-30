<?php

declare(strict_types=1);

namespace App\Dto;

use App\Domain\Billing\TokenCounts;

/**
 * Событие использования из JSONL (проверено парсером).
 */
final class UsageEventData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts,
        public readonly TokenCounts $counts,
    ) {
    }
}
