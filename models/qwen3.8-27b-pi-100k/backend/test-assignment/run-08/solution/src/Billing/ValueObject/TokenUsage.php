<?php

declare(strict_types=1);

namespace App\Billing\ValueObject;

/**
 * Расход токенов в одном запросе (целые ≥ 0).
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

    /** Всего токенов (для единой схемы тарификации). */
    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
