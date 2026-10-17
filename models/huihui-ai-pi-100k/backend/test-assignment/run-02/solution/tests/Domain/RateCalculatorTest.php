<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Iso8601;
use App\Domain\OffPeakWindow;
use App\Domain\PricingUnit;
use App\Domain\RateCalculator;
use App\Domain\TokenUsage;
use PHPUnit\Framework\TestCase;

final class RateCalculatorTest extends TestCase
{
    /**
     * Эталонный случай из раздела «Расчёт стоимости» TASK.md.
     */
    public function testExactScenarioFromTask(): void
    {
        $window = OffPeakWindow::parse('16:30', '00:30', '0.5');
        $ts = new \DateTimeImmutable('2026-09-15T11:00:00+00:00'); // 11:00 — вне окна

        // e1: lumen-8, OLD цены (valid_from 2026-08-01), ts 2026-09-15T11:00
        $e1 = RateCalculator::eventCost(
            new TokenUsage(1200000, 800000, 2500000, 83333),
            PricingUnit::PerMillionPerKind,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            $window,
            $ts,
        );
        self::assertSame('14.262499', $e1->format());

        // e2: lumen-8, NEW цены (valid_from 2026-09-15T12:00, включительно), ts 12:00
        $e2 = RateCalculator::eventCost(
            new TokenUsage(2000000, 1000000, 0, 0),
            PricingUnit::PerMillionPerKind,
            ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.150000', 'cache_write' => '1.875000'],
            $window,
            new \DateTimeImmutable('2026-09-15T12:00:00+00:00'),
        );
        self::assertSame('11.000000', $e2->format());

        // e3: gyro-pro, unified-схема
        $e3 = RateCalculator::eventCost(
            new TokenUsage(3000000, 500000, 0, 0),
            PricingUnit::PerMillionUnified,
            ['any' => '2.400000'],
            null,
            new \DateTimeImmutable('2026-09-20T17:10:00+00:00'),
        );
        self::assertSame('8.400000', $e3->format());
    }

    public function testOffPeakMultiplierApplied(): void
    {
        $window = OffPeakWindow::parse('16:30', '00:30', '0.5');
        $usage = new TokenUsage(1000000, 0, 0, 0);
        $rates = ['input' => '1.000000', 'output' => '2.000000', 'cache_read' => '0.250000', 'cache_write' => '1.250000'];

        // 1 000 000 * 1.0 / 1e6 = 1.0 вне окна; внутри окна 1.0 * 0.5 = 0.5
        $full = RateCalculator::eventCost($usage, PricingUnit::PerMillionPerKind, $rates, $window, new \DateTimeImmutable('2026-10-05T12:00:00+00:00'));
        $half = RateCalculator::eventCost($usage, PricingUnit::PerMillionPerKind, $rates, $window, new \DateTimeImmutable('2026-10-05T23:00:00+00:00'));

        self::assertSame('1.000000', $full->format());
        self::assertSame('0.500000', $half->format());
    }

    public function testRoundingAppliedOnceAtTheEnd(): void
    {
        $usage = new TokenUsage(0, 0, 0, 1000);
        $rates = ['input' => '0', 'output' => '0', 'cache_read' => '0', 'cache_write' => '3.750000'];

        $cost = RateCalculator::eventCost(
            $usage,
            PricingUnit::PerMillionPerKind,
            $rates,
            null,
            new \DateTimeImmutable('2026-10-05T00:00:00+00:00'),
        );

        // 1000 * 3.75 / 1 000 000 = 0.00375 → 0.003750 (ровно, без «двойного округления»)
        self::assertSame('0.003750', $cost->format());
    }

    public function testZeroTokensCostZero(): void
    {
        $cost = RateCalculator::eventCost(
            new TokenUsage(0, 0, 0, 0),
            PricingUnit::PerMillionUnified,
            ['any' => '2.400000'],
            null,
            new \DateTimeImmutable('2026-10-05T00:00:00+00:00'),
        );

        self::assertSame('0.000000', $cost->format());
    }
}
