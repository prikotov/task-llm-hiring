<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

use InvalidArgumentException;

final readonly class TokenUsage
{
    public function __construct(
        public int $input,
        public int $output,
        public int $cacheRead,
        public int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $value) {
            if ($value < 0) {
                throw new InvalidArgumentException('Количество токенов не может быть отрицательным');
            }
        }
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
