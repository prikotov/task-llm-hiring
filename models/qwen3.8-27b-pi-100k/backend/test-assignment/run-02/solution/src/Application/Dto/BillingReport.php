<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Сводка для app:billing:report — уже сгруппирована и отсортирована
 * (user, затем provider/code, лексикографически).
 *
 * @param list<array{user: string, model: string, tokens: array{input: int, output: int, cache_read: int, cache_write: int}, cost: string}> $rows
 * @param array{input: int, output: int, cache_read: int, cache_write: int} $totalTokens
 * @param string $totalCost
 */
final readonly class BillingReport
{
    public function __construct(
        public array $rows,
        public array $totalTokens,
        public string $totalCost,
    ) {
    }
}
