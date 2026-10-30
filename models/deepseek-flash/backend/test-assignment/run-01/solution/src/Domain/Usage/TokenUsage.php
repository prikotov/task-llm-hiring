<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Money\Decimal;
use InvalidArgumentException;

final readonly class TokenUsage
{
    public function __construct(
        public int $input,
        public int $output,
        public int $cacheRead,
        public int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $tokens) {
            if ($tokens < 0) {
                throw new InvalidArgumentException('Token counts must be non-negative.');
            }
        }
    }

    public function total(): Decimal
    {
        return Decimal::fromInt($this->input)
            ->add(Decimal::fromInt($this->output))
            ->add(Decimal::fromInt($this->cacheRead))
            ->add(Decimal::fromInt($this->cacheWrite));
    }
}
