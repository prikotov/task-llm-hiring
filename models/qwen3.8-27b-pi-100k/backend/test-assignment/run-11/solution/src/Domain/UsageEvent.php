<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Событие использования в терминах домена (не сущность БД).
 * Служит входом расчёта: RateCalculator не должен знать о Doctrine.
 */
final class UsageEvent
{
    private function __construct(
        public readonly int $id,
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

    public static function with(
        int $id,
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        array $tokens,
    ): self {
        return new self(
            $id,
            $provider,
            $providerRequestId,
            $model,
            $user,
            $ts,
            (int) ($tokens['input'] ?? 0),
            (int) ($tokens['output'] ?? 0),
            (int) ($tokens['cache_read'] ?? 0),
            (int) ($tokens['cache_write'] ?? 0),
        );
    }

    public function totalTokens(): int
    {
        return $this->tokensInput + $this->tokensOutput + $this->tokensCacheRead + $this->tokensCacheWrite;
    }
}
