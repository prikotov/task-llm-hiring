<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use App\Domain\ReportCsv;
use PHPUnit\Framework\TestCase;

final class ReportCsvTest extends TestCase
{
    public function testRenderMatchesTaskExampleByteForByte(): void
    {
        $csv = ReportCsv::render(
            [
                [
                    'user' => 'u-alice',
                    'model' => 'prism/lumen-8',
                    'tokens' => [1_200_000, 800_000, 2_500_000, 83_333],
                    'cost' => Money::fromDecimal('14.262499'),
                ],
                [
                    'user' => 'u-alice',
                    'model' => 'sfera/gyro-pro',
                    'tokens' => [3_000_000, 500_000, 0, 0],
                    'cost' => Money::fromDecimal('8.400000'),
                ],
                [
                    'user' => 'u-bob',
                    'model' => 'prism/lumen-8',
                    'tokens' => [2_000_000, 1_000_000, 0, 0],
                    'cost' => Money::fromDecimal('11.000000'),
                ],
            ],
            [6_200_000, 2_300_000, 2_500_000, 83_333],
            Money::fromDecimal('33.662499'),
        );

        $expected = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
            . "u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
            . "u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
            . "u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
            . "TOTAL,,6200000,2300000,2500000,83333,33.662499\n";

        self::assertSame($expected, $csv);
    }

    public function testFieldsWithSpecialCharactersAreQuoted(): void
    {
        $csv = ReportCsv::render(
            [
                [
                    'user' => 'u,alice',
                    'model' => 'p/m',
                    'tokens' => [1, 1, 0, 0],
                    'cost' => Money::fromDecimal('1.000000'),
                ],
            ],
            [1, 1, 0, 0],
            Money::fromDecimal('1.000000'),
        );

        self::assertStringContainsString('"u,alice",p/m,1,1,0,0,1.000000', $csv);
    }

    public function testEmptyReportContainsHeaderAndTotalOnly(): void
    {
        $csv = ReportCsv::render([], [0, 0, 0, 0], Money::zero());

        self::assertSame(
            "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
            . "TOTAL,,0,0,0,0,0.000000\n",
            $csv,
        );
    }
}
