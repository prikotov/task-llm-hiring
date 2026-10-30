<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Time\UtcInstant;

/**
 * Разобранное и валидированное событие использования (входные данные,
 * до регистрации в БД).
 */
final class UsageEventDraft
{
    /**
     * @param int $tokensInput     токены input
     * @param int $tokensOutput    токены output
     * @param int $tokensCacheRead токены cache_read
     * @param int $tokensCacheWrite токены cache_write
     */
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts,
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
    ) {}

    public function totalTokens(): int
    {
        return $this->tokensInput + $this->tokensOutput + $this->tokensCacheRead + $this->tokensCacheWrite;
    }
}
