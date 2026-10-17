<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\TokenUsage;

/**
 * Событие использования из JSONL-файла (после валидации).
 */
final class UsageEventInput
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts,
        public readonly TokenUsage $tokens,
    ) {
    }

    /** Ключ дедупликации (provider, provider_request_id). */
    public function deduplicationKey(): string
    {
        return $this->provider . "\x1E" . $this->providerRequestId;
    }
}
