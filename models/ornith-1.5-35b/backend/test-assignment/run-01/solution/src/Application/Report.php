<?php

declare(strict_types=1);

namespace App\Application;

/**
 * Итог тарификации всех обработанных событий.
 */
final class UsageReport
{
    /**
     * @param list<ReportLine> $lines строки по событиям (в порядке выборки)
     * @param array<int,int>    $counts количество событий: rated / unrated / total
     */
    public function __construct(
        private readonly array $lines,
        private readonly int $rated,
        private readonly int $unrated,
        private readonly int $total
    ) {
    }

    /**
     * @return list<ReportLine>
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getRated(): int
    {
        return $this->rated;
    }

    public function getUnrated(): int
    {
        return $this->unrated;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
