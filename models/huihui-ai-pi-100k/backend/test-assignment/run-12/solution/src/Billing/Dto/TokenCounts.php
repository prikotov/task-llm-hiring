<?php

declare(strict_types=1);

namespace App\Billing\Dto;

use InvalidArgumentException;

final class TokenCounts
{
    /**
     * @param int $input          токены input
     * @param int $output         токены output
     * @param int $cacheRead      токены cache_read
     * @param int $cacheWrite     токены cache_write
     */
    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $n) {
            if ($n < 0 || (string) $n !== (string) (int) $n || $n > PHP_INT_MAX) {
                throw new InvalidArgumentException('Token counts must be non-negative integers.');
            }
        }
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }

    /** @return array{0:int,1:int,2:int,3:int} (input, output, cache_read, cache_write) */
    public function asArray(): array
    {
        return [$this->input, $this->output, $this->cacheRead, $this->cacheWrite];
    }

    public static function fromArray(array $a): self
    {
        return new self($a[0], $a[1], $a[2], $a[3]);
    }
}
