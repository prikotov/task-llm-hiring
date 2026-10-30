<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\CostCalculator;
use App\Domain\Unit;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private const PRICE_PER_1M = [
        'unit' => Unit::PER_1M_TOKENS,
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
        'any' => null,
    ];

    private const PRICE_UNIFIED = [
        'unit' => Unit::PER_1M_TOKENS_UNIFIED,
        'input' => null,
        'output' => null,
        'cache_read' => null,
        'cache_write' => null,
        'any' => '1.500000',
    ];

    public function testFixtureEventSingleRounding(): void
    {
        // 1.2M*3 + 0.8M*12 + 2.5M*0.3 + 83333*3.75 = 14262498.75 / 1e6
        // Сумма до округления: 14.26249875 → единственное HALF_UP-округление до 6 знаков.
        $result = (new CostCalculator())->cost(
            1200000,
            800000,
            2500000,
            83333,
            self::PRICE_PER_1M,
            null,
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );

        self::assertSame('14.262499', $result);
    }

    public function testUnifiedUnitSumsAllCounts(): void
    {
        $result = (new CostCalculator())->cost(
            1200000,
            800000,
            2500000,
            83333,
            self::PRICE_UNIFIED,
            null,
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );

        self::assertSame('6.875000', $result);
    }

    public function testOffPeakInsideWindowCrossingMidnight(): void
    {
        $base = (new CostCalculator())->cost(1000000, 0, 0, 0, self::PRICE_PER_1M, null, new DateTimeImmutable('2026-02-01T13:00:00Z'));
        self::assertSame('3.000000', $base);

        $offPeak = ['start' => '23:00', 'end' => '01:00', 'multiplier' => '0.5'];

        $inside = (new CostCalculator())->cost(1000000, 0, 0, 0, self::PRICE_PER_1M, $offPeak, new DateTimeImmutable('2026-02-01T00:30:00Z'));
        $startBorder = (new CostCalculator())->cost(1000000, 0, 0, 0, self::PRICE_PER_1M, $offPeak, new DateTimeImmutable('2026-02-01T23:00:00Z'));
        $endBorder = (new CostCalculator())->cost(1000000, 0, 0, 0, self::PRICE_PER_1M, $offPeak, new DateTimeImmutable('2026-02-01T01:00:00Z'));
        $outside = (new CostCalculator())->cost(1000000, 0, 0, 0, self::PRICE_PER_1M, $offPeak, new DateTimeImmutable('2026-02-01T13:00:00Z'));

        self::assertSame('1.500000', $inside);
        self::assertSame('1.500000', $startBorder);
        self::assertSame('3.000000', $endBorder);
        self::assertSame('3.000000', $outside);
    }

    public function testOffPeakAppliedAfterSummingThenSingleRounding(): void
    {
        // До скид: 14.26249875; ×0.1 = 1.426249875 → 1.426250
        $result = (new CostCalculator())->cost(
            1200000,
            800000,
            2500000,
            83333,
            self::PRICE_PER_1M,
            ['start' => '00:00', 'end' => '23:59', 'multiplier' => '0.1'],
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );

        self::assertSame('1.426250', $result);
    }

    public function testUnknownUnitThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new CostCalculator())->cost(1, 0, 0, 0, [...self::PRICE_PER_1M, 'unit' => 'bogus'], null, new DateTimeImmutable('2026-09-15T11:00:00Z'));
    }

    public function testZeroTokensGiveZeroCost(): void
    {
        $result = (new CostCalculator())->cost(0, 0, 0, 0, self::PRICE_PER_1M, null, new DateTimeImmutable('2026-09-15T11:00:00Z'));

        self::assertSame('0.000000', $result);
    }
}
