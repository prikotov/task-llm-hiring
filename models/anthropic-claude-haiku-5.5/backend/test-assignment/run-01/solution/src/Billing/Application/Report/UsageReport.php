<?php

declare(strict_types=1);

namespace App\Billing\Application\Report;

/**
 * Отчёт по (user, model). Все суммы — строки в десятичной записи.
 */
final readonly class UsageReport
{
    /**
     * @param list<UsageReportRow> $rows
     */
    public function __construct(
        public array $rows,
        public UsageReportRow $total,
    ) {
    }
}
