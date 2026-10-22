<?php

declare(strict_types=1);

namespace App\Tests\Domain\Pricing;

use App\Domain\Money\Decimal;
use App\Domain\Pricing\CostCalculator;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\RateCard;
use App\Domain\Usage\TokenUsage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private CostCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new CostCalculator();
    }

    public function testFixtureEventE1(): void
    {
        $usage = new TokenUsage(1_200_000, 800_000, 2_500_000, 83_333);
        $rates = RateCard::perToken(
            Decimal::fromString('3.000000'),
            Decimal::fromString('12.000000'),
            Decimal::fromString('0.300000'),
            Decimal::fromString('3.750000'),
        );

        $cost = $this->calculator->calculate(
            $usage,
            $rates,
            OffPeakWindow::fromStrings('16:30', '00:30', '0.5'),
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );

        self::assertSame('14.262499', $cost->format(6));
    }

    public function testFixtureEventE2UsesNewRates(): void
    {
        $usage = new TokenUsage(2_000_000, 1_000_000, 0, 0);
        $rates = RateCard::perToken(
            Decimal::fromString('1.500000'),
            Decimal::fromString('8.000000'),
            Decimal::fromString('0.150000'),
            Decimal::fromString('1.875000'),
        );

        $cost = $this->calculator->calculate(
            $usage,
            $rates,
            null,
            new DateTimeImmutable('2026-09-15T12:00:00Z'),
        );

        self::assertSame('11.000000', $cost->format(6));
    }

    public function testUnifiedScheme(): void
    {
        $usage = new TokenUsage(3_000_000, 500_000, 0, 0);
        $rates = RateCard::unified(Decimal::fromString('2.400000'));

        $cost = $this->calculator->calculate(
            $usage,
            $rates,
            null,
            new DateTimeImmutable('2026-09-20T17:10:00Z'),
        );

        self::assertSame('8.400000', $cost->format(6));
    }

    public function testOffPeakMultipliesWholeEventBeforeFinalRounding(): void
    {
        $usage = new TokenUsage(1_200_000, 800_000, 2_500_000, 83_333);
        $rates = RateCard::perToken(
            Decimal::fromString('3.000000'),
            Decimal::fromString('12.000000'),
            Decimal::fromString('0.300000'),
            Decimal::fromString('3.750000'),
        );

        $cost = $this->calculator->calculate(
            $usage,
            $rates,
            OffPeakWindow::fromStrings('16:30', '00:30', '0.5'),
            new DateTimeImmutable('2026-09-15T17:00:00Z'),
        );

        // 14.26249875 * 0.5 = 7.131249375 -> 7.131249. Rounding first would
        // have produced 7.131250, so this assertion guards the required order.
        self::assertSame('7.131249', $cost->format(6));
    }

    public function testZeroTokensCostZero(): void
    {
        $cost = $this->calculator->calculate(
            new TokenUsage(0, 0, 0, 0),
            RateCard::unified(Decimal::fromString('2.4')),
            null,
            new DateTimeImmutable('2026-09-20T17:10:00Z'),
        );

        self::assertSame('0.000000', $cost->format(6));
    }
}
