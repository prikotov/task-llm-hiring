<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Потребление токенов в одном событии.
 * Количество — целое неотрицательное; любой вид (и all сразу) может быть нулевым.
 */
final class TokenUsage
{
    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }

    public function count(TokenKind $kind): int
    {
        return match ($kind) {
            TokenKind::Input => $this->input,
            TokenKind::Output => $this->output,
            TokenKind::CacheRead => $this->cacheRead,
            TokenKind::CacheWrite => $this->cacheWrite,
            TokenKind::Any => $this->total(),
        };
    }

    public static function ofZero(): self
    {
        return new self(0, 0, 0, 0);
    }
}
