<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Billing\CostCalculator;
use App\Domain\Catalog\OffPeak;
use App\Domain\Catalog\PricingUnit;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private const AUGUST_RATES = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    public function testSeparateRatesEventFromTaskExample(): void
    {
        $cost = (new CostCalculator())->calculate(
            1200000,
            800000,
            2500000,
            83333,
            PricingUnit::Per1mTokens,
            self::AUGUST_RATES,
            null,
            self::ts('2026-09-15T11:00:00Z'),
        );

        // 3.6 + 9.6 + 0.75 + 0.31249875 = 14.26249875 -> HALF_UP -> 14.262499
        self::assertSame('14.262499', $cost);
    }

    public function testUnifiedRate(): void
    {
        $cost = (new CostCalculator())->calculate(
            3000000,
            500000,
            0,
            0,
            PricingUnit::Per1mTokensUnified,
            ['any' => '2.400000'],
            null,
            self::ts('2026-09-20T17:10:00Z'),
        );

        // 3.5M x 2.4 / 1M = 8.4
        self::assertSame('8.400000', $cost);
    }

    public function testOffPeakMultiplierAppliedInsideWindow(): void
    {
        $offPeak = new OffPeak('16:30', '00:30', '0.5');

        $peak = (new CostCalculator())->calculate(
            2000000,
            1000000,
            0,
            0,
            PricingUnit::Per1mTokens,
            ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.150000', 'cache_write' => '1.875000'],
            null,
            self::ts('2026-09-15T12:00:00Z'),
        );

        $off = (new CostCalculator())->calculate(
            2000000,
            1000000,
            0,
            0,
            PricingUnit::Per1mTokens,
            ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.150000', 'cache_write' => '1.875000'],
            $offPeak,
            self::ts('2026-10-01T18:00:00Z'),
        );

        self::assertSame('11.000000', $peak);
        // (2M x 1.5 + 1M x 8) x 0.5 = 11 x 0.5 = 5.5
        self::assertSame('5.500000', $off);
    }

    private static function ts(string $iso): \DateTimeImmutable
    {
        return new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
    }
}
