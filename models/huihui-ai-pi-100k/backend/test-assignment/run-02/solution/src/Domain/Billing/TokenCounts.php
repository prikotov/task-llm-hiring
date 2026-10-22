<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Четыре вида потреблённых токенов (целые ≥ 0). Чистый носитель значений
 * для расчёта стоимости.
 */
final class TokenCounts
{
    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
    }

    /**
     * Суммарное число токенов по всем видам (для единого тарифа).
     */
    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
