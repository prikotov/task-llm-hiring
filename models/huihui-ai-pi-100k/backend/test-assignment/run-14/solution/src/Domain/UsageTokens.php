<?php

declare(strict_types=1);

namespace App\Domain;

/** Разбивка потраченных токенов по видам — одна строка события. */
final class UsageTokens
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
}
