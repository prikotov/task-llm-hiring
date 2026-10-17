<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

/**
 * Потребление токенов событием, разбитое по видам.
 * Все значения — целые неотрицательные числа.
 */
final class TokenUsage
{
    public function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
        foreach ([$input, $output, $cacheRead, $cacheWrite] as $value) {
            if ($value < 0) {
                throw new ValueError('TokenUsage: количество токенов не может быть отрицательным: ' . $value);
            }
        }
    }

    /** Суммарное число токенов всех видов. */
    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }

    /**
     * Количество токенов по видам: «input», «output», «cache_read», «cache_write».
     *
     * @return array<string, int>
     */
    public function byKind(): array
    {
        return [
            'input' => $this->input,
            'output' => $this->output,
            'cache_read' => $this->cacheRead,
            'cache_write' => $this->cacheWrite,
        ];
    }
}
