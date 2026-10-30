<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Валидированное событие использования (строка JSONL).
 *
 * @readonly
 */
final class UsageRecord
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts, // UTC
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
    ) {
    }

    /**
     * @param array{
     *   provider:string, model:string, user:string,
     *   provider_request_id:string, ts:\DateTimeImmutable,
     *   tokens_input:int, tokens_output:int, tokens_cache_read:int, tokens_cache_write:int,
     * } $data — нормализованные данные строки (см. UsageEventParser)
     */
    public static function fromParsed(array $data): self
    {
        return new self(
            provider: $data['provider'],
            providerRequestId: $data['provider_request_id'],
            model: $data['model'],
            user: $data['user'],
            ts: $data['ts'],
            tokensInput: $data['tokens_input'],
            tokensOutput: $data['tokens_output'],
            tokensCacheRead: $data['tokens_cache_read'],
            tokensCacheWrite: $data['tokens_cache_write'],
        );
    }

    /**
     * @return array{input:int, output:int, cache_read:int, cache_write:int}
     */
    public function tokenCounts(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
    }
}
