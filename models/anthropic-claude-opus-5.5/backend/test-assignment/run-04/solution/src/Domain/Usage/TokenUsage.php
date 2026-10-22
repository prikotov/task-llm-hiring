<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use InvalidArgumentException;

/**
 * Количества израсходованных токенов по видам. Все значения — целые >= 0.
 */
final readonly class TokenUsage
{
    public function __construct(
        public int $input = 0,
        public int $output = 0,
        public int $cacheRead = 0,
        public int $cacheWrite = 0,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $value) {
            if ($value < 0) {
                throw new InvalidArgumentException('Количество токенов не может быть отрицательным.');
            }
        }
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
