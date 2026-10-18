<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\Billing\TokenCounts;
use DateTimeImmutable;

/**
 * Разобранное и проверенное событие из JSONL-файла.
 */
final readonly class UsageEventSpec
{
    public function __construct(
        public int $line,
        public string $provider,
        public string $providerRequestId,
        public string $model,
        public string $user,
        public DateTimeImmutable $ts,
        public TokenCounts $tokens,
    ) {
    }
}
