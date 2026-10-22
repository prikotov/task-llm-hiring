<?php

declare(strict_types=1);

namespace App\Billing\Application\Reporting;

use App\Billing\Domain\Money\Money;

/**
 * Сборка отчёта: агрегаты из ReportQuery + сортировка + итоговая строка.
 *
 * Итог — сумма уже округлённых стоимостей событий, без повторного округления
 * (TASK п. 4). Сортировка — лексикографическая по (user, model) в порядке
 * байтов UTF-8 (strcmp), а не по collation БД: результат не должен зависеть
 * от локали сервера.
 */
final class BuildReport
{
    public function __construct(private readonly ReportQuery $query)
    {
    }

    public function __invoke(ReportPeriod $period): Report
    {
        $rows = $this->query->groups($period);

        usort($rows, static function (ReportGroupRow $a, ReportGroupRow $b): int {
            return strcmp($a->user, $b->user) <=> 0 ?: strcmp($a->model(), $b->model());
        });

        $total = new ReportGroupRow(
            user: 'TOTAL',
            providerCode: '',
            modelCode: '',
            tokensInput: array_sum(array_map(static fn (ReportGroupRow $r): int => $r->tokensInput, $rows)),
            tokensOutput: array_sum(array_map(static fn (ReportGroupRow $r): int => $r->tokensOutput, $rows)),
            tokensCacheRead: array_sum(array_map(static fn (ReportGroupRow $r): int => $r->tokensCacheRead, $rows)),
            tokensCacheWrite: array_sum(array_map(static fn (ReportGroupRow $r): int => $r->tokensCacheWrite, $rows)),
            cost: array_reduce(
                $rows,
                static fn (Money $carry, ReportGroupRow $row): Money => $carry->add($row->cost),
                Money::zero(Money::MONEY_SCALE),
            ),
        );

        return new Report(array_values($rows), $total);
    }
}
