<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\OffPeakWindow;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\PricingUnit;
use App\Domain\Usage\EventPricingCalculator;
use App\Domain\Usage\UsageEvent;
use PHPUnit\Framework\TestCase;

final class EventPricingCalculatorTest extends TestCase
{
    private EventPricingCalculator $calculator;

    private Model $model;

    protected function setUp(): void
    {
        $this->calculator = new EventPricingCalculator();
        $this->model = new Model('prism', 'lumen-8');
    }

    private function event(string $ts, int $in, int $out, int $cr, int $cw): UsageEvent
    {
        return new UsageEvent('prism', 'req-1', $this->model, 'u-alice', new \DateTimeImmutable($ts), $in, $out, $cr, $cw);
    }

    public function testPer1MTokensMatchesWorkedExample(): void
    {
        $price = new PriceRow(
            $this->model,
            new \DateTimeImmutable('2026-08-01T00:00:00+00:00'),
            'USD',
            PricingUnit::Per1MTokens,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
        );

        // e1: 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75 / 1M = 14.26249875 → 14.262499
        $cost = $this->calculator->computeEventCost(
            $this->event('2026-09-15T11:00:00+00:00', 1_200_000, 800_000, 2_500_000, 83_333),
            $price,
            null,
        );
        self::assertSame('14.262499', $cost->asFixed6String());
    }

    public function testPer1MTokensUnifiedMatchesWorkedExample(): void
    {
        $model = new Model('sfera', 'gyro-pro');
        $price = new PriceRow(
            $model,
            new \DateTimeImmutable('2026-08-01T00:00:00+00:00'),
            'USD',
            PricingUnit::Per1MTokensUnified,
            ['any' => '2.400000'],
        );

        // e3: (3M + 0.5M) × 2.4 / 1M = 8.4
        $event = new UsageEvent('sfera', 'req-2', $model, 'u-alice', new \DateTimeImmutable('2026-09-20T17:10:00+00:00'), 3_000_000, 500_000, 0, 0);
        $cost = $this->calculator->computeEventCost($event, $price, null);
        self::assertSame('8.400000', $cost->asFixed6String());
    }

    public function testOffPeakMultiplierAppliedToWholeEvent(): void
    {
        $price = new PriceRow(
            $this->model,
            new \DateTimeImmutable('2026-08-01T00:00:00+00:00'),
            'USD',
            PricingUnit::Per1MTokens,
            ['input' => '0.400000', 'output' => '1.600000', 'cache_read' => '0.040000', 'cache_write' => '0.500000'],
        );
        $offPeak = new OffPeakWindow('16:30', '00:30', '0.5');

        // 1M input × 0.4 / 1M = 0.4; 17:00 — в окне → ×0.5 = 0.2
        $inWindow = $this->calculator->computeEventCost($this->event('2026-09-16T17:00:00+00:00', 1_000_000, 0, 0, 0), $price, $offPeak);
        self::assertSame('0.200000', $inWindow->asFixed6String());

        // 11:00 — вне окна → 0.4
        $peak = $this->calculator->computeEventCost($this->event('2026-09-16T11:00:00+00:00', 1_000_000, 0, 0, 0), $price, $offPeak);
        self::assertSame('0.400000', $peak->asFixed6String());
    }

    public function testOffPeakMultiplierRoundedOnceAtEventLevel(): void
    {
        $price = new PriceRow(
            $this->model,
            new \DateTimeImmutable('2026-08-01T00:00:00+00:00'),
            'USD',
            PricingUnit::Per1MTokens,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
        );
        $offPeak = new OffPeakWindow('00:00', '23:59', '0.5');

        // 14.26249875 × 0.5 = 7.131249375 → 7.131249 (округление только итога)
        $cost = $this->calculator->computeEventCost(
            $this->event('2026-09-15T23:00:00+00:00', 1_200_000, 800_000, 2_500_000, 83_333),
            $price,
            $offPeak,
        );
        self::assertSame('7.131249', $cost->asFixed6String());
    }

    public function testZeroTokensGiveZeroCost(): void
    {
        $price = new PriceRow(
            $this->model,
            new \DateTimeImmutable('2026-08-01T00:00:00+00:00'),
            'USD',
            PricingUnit::Per1MTokens,
            ['input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75'],
        );

        $cost = $this->calculator->computeEventCost($this->event('2026-09-15T11:00:00+00:00', 0, 0, 0, 0), $price, null);
        self::assertSame('0.000000', $cost->asFixed6String());
    }
}
