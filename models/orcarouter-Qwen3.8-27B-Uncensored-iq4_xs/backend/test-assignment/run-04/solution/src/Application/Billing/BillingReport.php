<?php

declare(strict_types=1);

namespace App\Application\Billing;

/**
 * Собранный отчёт: строки по (user, model), отсортированные по user, затем
 * по model (лексикографически), и итоговая строчка TOTAL.
 */
final class BillingReport
{
    /**
     * @param list<ReportRow> $rows
     * @param list<int>       $totalTokens [input, output, cache_read, cache_write]
     * @param string          $totalCostUsd
     */
    public function __construct(
        public readonly array $rows,
        public readonly array $totalTokens,
        public readonly string $totalCostUsd,
    ) {
    }
}
