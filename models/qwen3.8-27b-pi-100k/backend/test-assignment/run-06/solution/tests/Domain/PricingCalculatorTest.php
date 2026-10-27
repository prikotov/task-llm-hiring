<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\PriceUnit;
use App\Domain\Billing\PricingCalculator;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new PricingCalculator();
    }

    private function ts(string $iso): \DateTimeImmutable
    {
        return new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
    }

    public function testPerMillionTokensAllCategories(): void
    {
        $cost = $this->calc->eventCost(
            1_200_000,
            800_000,
            2_500_000,
            83_333,
            PriceUnit::Per1MToken,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            null,
            $this->ts('2026-09-15T11:00:00Z'),
        );

        // 1.2*3.0 + 0.8*12 + 2.5*0.3 + 83333*3.75/1e6 = 3.6 + 9.6 + 0.75 + 0.31249875
        self::assertSame('14.262499', $cost);
    }

    public function testPerMillionTokensAllZero(): void
    {
        $cost = $this->calc->eventCost(
            0, 0, 0, 0,
            PriceUnit::Per1MToken,
            ['input' => '5', 'output' => '5', 'cache_read' => '5', 'cache_write' => '5'],
            null,
            $this->ts('2026-09-15T11:00:00Z'),
        );

        self::assertSame('0.000000', $cost);
    }

    public function testUnifiedSchemeSumsAllTokenCategories(): void
    {
        $cost = $this->calc->eventCost(
            1_000_000,
            2_000_000,
            3_000_000,
            4_000_000,
            PriceUnit::Per1MTokenUnified,
            ['any' => '5.000000'],
            null,
            $this->ts('2026-09-15T11:00:00Z'),
        );

        self::assertSame('50.000000', $cost);
    }

    public function testRoundHalfUp6(): void
    {
        self::assertSame('0.000001', PricingCalculator::roundHalfUp6('0.0000005'));
        self::assertSame('0.000000', PricingCalculator::roundHalfUp6('0.0000004'));
        self::assertSame('1.500000', PricingCalculator::roundHalfUp6('1.4999995'));
        self::assertSame('1.500000', PricingCalculator::roundHalfUp6('1.5'));
    }

    public function testOffPeakMultiplierIsSingleRounding(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.500000');

        $cost = $this->calc->eventCost(
            1_000_000,
            0,
            0,
            0,
            PriceUnit::Per1MToken,
            ['input' => '3.000000', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
            $window,
            $this->ts('2026-09-10T18:00:00Z'),
        );

        self::assertSame('1.500000', $cost);
    }

    public function testOffPeakWindowNotHitIsIgnored(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.500000');

        $cost = $this->calc->eventCost(
            1_000_000,
            0, 0, 0,
            PriceUnit::Per1MToken,
            ['input' => '3.000000', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
            $window,
            $this->ts('2026-09-10T09:00:00Z'),
        );

        self::assertSame('3.000000', $cost);
    }
}
