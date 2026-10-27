<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Разбивка израсходованных токенов по видам. Все значения — целые ≥ 0.
 */
final class TokenMix
{
    /**
     * @param int $input        токены input
     * @param int $output       токены output
     * @param int $cacheRead    токены cache_read
     * @param int $cacheWrite   токены cache_write
     */
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
