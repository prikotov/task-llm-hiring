<?php

declare(strict_types=1);

namespace App\Domain\Usage;

final class TokenUsage
{
    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $v) {
            if ($v < 0) {
                throw new \InvalidArgumentException('Token counts must be >= 0.');
            }
        }
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
