<?php

declare(strict_types=1);

namespace App\Import;

/** Событие использования из JSONL (проверенное парсером). */
final class UsageSpec
{
    public function __construct(
        private readonly string $provider,
        private readonly string $providerRequestId,
        private readonly string $model,
        private readonly string $user,
        private readonly \DateTimeImmutable $ts,
        private readonly int $tokensInput,
        private readonly int $tokensOutput,
        private readonly int $tokensCacheRead,
        private readonly int $tokensCacheWrite,
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
        return $this->ts;
    }

    public function getTokensInput(): int
    {
        return $this->tokensInput;
    }

    public function getTokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function getTokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function getTokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }
}
