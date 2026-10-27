<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Событтие использования: provider, providerRequestId, model, user, ts, токены.
 *
 * `tokens` — map вида → неотрицательное целое (строкой, bcmath): `input`,
 * `output`, `cache_read`, `cache_write`.
 */
final class UsageEvent
{
    /** @param array{input:string, output:string, cache_read:string, cache_write:string} $tokens */
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
        public readonly string $model,
        public readonly string $user,
        public readonly \DateTimeImmutable $ts,
        public readonly array $tokens,
    ) {
    }

    /** @return array{input:string, output:string, cache_read:string, cache_write:string} */
    public function tokens(): array
    {
        return $this->tokens;
    }

    /** @param array<string,string> $tokens */
    public function tokensTotal(): string
    {
        $sum = '0';
        foreach ($this->tokens as $t) {
            $sum = bcadd($sum, (string) $t, 0);
        }
        return $sum;
    }
}
