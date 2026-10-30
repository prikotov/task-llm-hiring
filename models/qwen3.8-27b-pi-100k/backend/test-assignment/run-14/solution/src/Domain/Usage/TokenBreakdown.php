<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Pricing\Money;

/**
 * Четыре вида токенов одного запроса (целые, >= 0).
 */
final readonly class TokenBreakdown
{
    public function __construct(
        public int $input,
        public int $output,
        public int $cacheRead,
        public int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $value) {
            if ($value < 0) {
                throw new \InvalidArgumentException('Token counts must be >= 0.');
            }
        }
    }

    /**
     * Сумма всех видов как Money (целое десятичное значение, без округления).
     */
    public function tokenInput(): Money { return new Money((string) $this->input); }
    public function tokenOutput(): Money { return new Money((string) $this->output); }
    public function tokenCacheRead(): Money { return new Money((string) $this->cacheRead); }
    public function tokenCacheWrite(): Money { return new Money((string) $this->cacheWrite); }

    public function total(): Money
    {
        return new Money((string) ($this->input + $this->output + $this->cacheRead + $this->cacheWrite));
    }

    public function sumWith(self $other): self
    {
        return new self(
            $this->input + $other->input,
            $this->output + $other->output,
            $this->cacheRead + $other->cacheRead,
            $this->cacheWrite + $other->cacheWrite,
        );
    }
}
