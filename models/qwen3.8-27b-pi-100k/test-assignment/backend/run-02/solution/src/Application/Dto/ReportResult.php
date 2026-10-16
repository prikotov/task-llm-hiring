<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Собранный отчёт: строки данных (отсортированы по user, затем model)
 * и итоговая строка TOTAL.
 */
final class ReportResult
{
    /**
     * @param list<ReportRow> $rows
     */
    public function __construct(
        public readonly array $rows,
        public readonly ReportRow $total,
    ) {
    }
}
