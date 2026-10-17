<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Строка события использования из JSONL.
 */
final class UsageRow
{
    public function __construct(
        private readonly string $provider,
        private readonly string $providerRequestId,
        private readonly string $model,
        private readonly string $user,
        private readonly string $ts,
        private readonly int $tokensInput,
        private readonly int $tokensOutput,
        private readonly int $tokensCacheRead,
        private readonly int $tokensCacheWrite
    ) {
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->ts);
    }

    public function getKey(): string
    {
        return $this->provider . '/' . $this->providerRequestId;
    }
}
