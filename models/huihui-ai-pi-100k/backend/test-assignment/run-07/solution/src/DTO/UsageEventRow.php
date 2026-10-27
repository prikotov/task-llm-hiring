<?php

declare(strict_types=1);

namespace App\DTO;

/** Нормализованное событие использования (одна строка JSONL). */
final class UsageEventRow
{
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
    ) {
    }

    /** @return array{input: int, output: int, cache_read: int, cache_write: int} */
    public function tokens(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
    }
}
