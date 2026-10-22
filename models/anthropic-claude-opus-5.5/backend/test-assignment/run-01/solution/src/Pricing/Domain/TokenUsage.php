<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

use InvalidArgumentException;

/** Количество токенов по видам — объект-значение. */
final readonly class TokenUsage
{
    public function __construct(
        public int $input,
        public int $output,
        public int $cacheRead,
        public int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $n) {
            if ($n < 0) {
                throw new InvalidArgumentException('Количество токенов не может быть отрицательным');
            }
        }
    }

    public function total(): string
    {
        return bcadd(bcadd((string) $this->input, (string) $this->output), bcadd((string) $this->cacheRead, (string) $this->cacheWrite));
    }
}
