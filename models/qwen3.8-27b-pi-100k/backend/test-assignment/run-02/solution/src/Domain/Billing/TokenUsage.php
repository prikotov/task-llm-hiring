<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Потребление токенов одним запросом, разбивкой по видам.
 */
final class TokenUsage
{
    public function __construct(
        private readonly int $input,
        private readonly int $output,
        private readonly int $cacheRead,
        private readonly int $cacheWrite,
    ) {
    }

    public function input(): int
    {
        return $this->input;
    }

    public function output(): int
    {
        return $this->output;
    }

    public function cacheRead(): int
    {
        return $this->cacheRead;
    }

    public function cacheWrite(): int
    {
        return $this->cacheWrite;
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
