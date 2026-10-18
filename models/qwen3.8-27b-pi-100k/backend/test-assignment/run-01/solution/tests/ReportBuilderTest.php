<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\ReportBuilder;
use App\Domain\ReportRow;
use PHPUnit\Framework\TestCase;

final class ReportBuilderTest extends TestCase
{
    public function testGroupsSortAndTotals(): void
    {
        $rows = [
            new ReportRow('u-bob', 'prism', 'lumen-8', 2000000, 1000000, 0, 0, '11.000000'),
            new ReportRow('u-alice', 'sfera', 'gyro-pro', 3000000, 500000, 0, 0, '8.400000'),
            new ReportRow('u-alice', 'prism', 'lumen-8', 1200000, 800000, 2500000, 83333, '14.262499'),
        ];

        self::assertSame(
            "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
            ."u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
            ."u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
            ."u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
            ."TOTAL,,6200000,2300000,2500000,83333,33.662499\n",
            (new ReportBuilder())->build($rows),
        );
    }

    public function testEmptyReportHasHeaderAndZeroTotal(): void
    {
        self::assertSame(
            "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
            ."TOTAL,,0,0,0,0,0.000000\n",
            (new ReportBuilder())->build([]),
        );
    }

    public function testCsvEscapingByRfc4180(): void
    {
        $rows = [
            new ReportRow('a,b', 'p', 'm', 1, 0, 0, 0, '0.100000'),
            new ReportRow('q"q', 'p', 'm', 2, 0, 0, 0, '0.200000'),
        ];

        $csv = (new ReportBuilder())->build($rows);
        $lines = explode("\n", $csv);

        self::assertSame('"a,b",p/m,1,0,0,0,0.100000', $lines[1]);
        self::assertSame('"q""q",p/m,2,0,0,0,0.200000', $lines[2]);
    }
}
