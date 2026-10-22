<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\CostCalculator;
use App\Billing\OffPeakWindow;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private const RATES = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    /** Из раздела "Пример расчёта" TASK.md. */
    public function testTaskExample(): void
    {
        $cost = CostCalculator::computeEventCost(
            [
                'input' => 1_200_000,
                'output' => 800_000,
                'cache_read' => 2_500_000,
                'cache_write' => 83_333,
            ],
            self::perTokenRow(),
            null,
            self::ts('2026-09-18T11:07:29Z'),
        );

        self::assertSame('14.262499', $cost);
    }

    public function testOffPeakMultiplierHalvesResult(): void
    {
        $window = new OffPeakWindow(16 * 60 + 30, 30, '0.5');

        $cost = CostCalculator::computeEventCost(
            [
                'input' => 1_200_000,
                'output' => 800_000,
                'cache_read' => 2_500_000,
                'cache_write' => 83_333,
            ],
            self::perTokenRow(),
            $window,
            self::ts('2026-09-18T18:00:00Z'),
        );

        // 14.26249875 * 0.5 = 7.131249375 -> 7.131249
        self::assertSame('7.131249', $cost);
    }

    public function testUnifiedRateSumsAllTokenKinds(): void
    {
        $row = new PriceRow(
            new Provider('sfera'),
            new Model(new Provider('sfera'), 'gyro-pro'),
            new DateTimeImmutable('2026-08-01', new DateTimeZone('UTC')),
            'USD',
            PriceRow::UNIT_UNIFIED,
            ['any' => '2.400000'],
        );

        $cost = CostCalculator::computeEventCost(
            [
                'input' => 3_000_000,
                'output' => 500_000,
                'cache_read' => 0,
                'cache_write' => 0,
            ],
            $row,
            null,
            self::ts('2026-09-01T04:12:45Z'),
        );

        self::assertSame('8.400000', $cost); // (3,000,000 + 500,000) * 2.4 / 1e6
    }

    public function testZeroTokensGiveZeroCost(): void
    {
        $cost = CostCalculator::computeEventCost(
            ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            self::perTokenRow(),
            null,
            self::ts('2026-09-18T11:07:29Z'),
        );

        self::assertSame('0.000000', $cost);
    }

    private static function perTokenRow(): PriceRow
    {
        $provider = new Provider('prism');

        return new PriceRow(
            $provider,
            new Model($provider, 'lumen-8'),
            new DateTimeImmutable('2026-08-01', new DateTimeZone('UTC')),
            'USD',
            PriceRow::UNIT_PER_TOKEN,
            self::RATES,
        );
    }

    private static function ts(string $iso): DateTimeImmutable
    {
        return new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }
}
