<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Billing\OffPeakPolicy;
use App\Domain\Billing\RateCalculator;
use App\Domain\Billing\TokenCounts;
use App\Domain\Pricing\Model;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\Provider;
use PHPUnit\Framework\TestCase;

final class RateCalculatorTest extends TestCase
{
    private const PRISM_OFF_PEAK = 990;  // 16:30
    private const OFF_PEAK_END = 30;     // 00:30

    private function row(PriceUnit $unit, array $rates): PriceRow
    {
        $provider = new Provider('prism', 'Prism AI');
        $model = new Model('lumen-8', $provider);

        return new PriceRow(
            $provider,
            $model,
            new \DateTimeImmutable('2026-08-01T00:00:00Z'),
            'USD',
            $unit,
            $rates['input'] ?? null,
            $rates['output'] ?? null,
            $rates['cache_read'] ?? null,
            $rates['cache_write'] ?? null,
            $rates['any'] ?? null,
        );
    }

    public function testFixtureEvent1Per1mTokens(): void
    {
        // 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M = 14.26249875 → 14.262499
        $cost = (new RateCalculator())->eventCost(
            new TokenCounts(1200000, 800000, 2500000, 83333),
            $this->row(PriceUnit::Per1mTokens, [
                'input' => '3.000000',
                'output' => '12.000000',
                'cache_read' => '0.300000',
                'cache_write' => '3.750000',
            ]),
            new OffPeakPolicy(self::PRISM_OFF_PEAK, self::OFF_PEAK_END, '0.5'),
            11 * 60, // 11:00 — пик
        );

        self::assertSame('14.262499', $cost);
    }

    public function testFixtureEvent3Unified(): void
    {
        $provider = new Provider('sfera', 'Sfera Cloud');
        $model = new Model('gyro-pro', $provider);
        $row = new PriceRow(
            $provider,
            $model,
            new \DateTimeImmutable('2026-08-01T00:00:00Z'),
            'USD',
            PriceUnit::Per1mTokensUnified,
            null,
            null,
            null,
            null,
            '2.400000',
        );

        // (3M + 0.5M) × 2.4 / 1M = 8.4
        self::assertSame(
            '8.400000',
            (new RateCalculator())->eventCost(
                new TokenCounts(3000000, 500000, 0, 0),
                $row,
                null,
                17 * 60 + 10,
            ),
        );
    }

    public function testZeroTokensCostZero(): void
    {
        $cost = (new RateCalculator())->eventCost(
            new TokenCounts(0, 0, 0, 0),
            $this->row(PriceUnit::Per1mTokens, [
                'input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75',
            ]),
            null,
            60,
        );

        self::assertSame('0.000000', $cost);
    }

    public function testOffPeakMultiplierAppliesInWindowAcrossMidnight(): void
    {
        $calculator = new RateCalculator();
        $row = $this->row(PriceUnit::Per1mTokens, [
            'input' => '1.000000',
            'output' => '1.000000',
            'cache_read' => '1.000000',
            'cache_write' => '1.000000',
        ]);
        $tokens = new TokenCounts(1000000, 0, 0, 0); // base = 1.0
        $offPeak = new OffPeakPolicy(self::PRISM_OFF_PEAK, self::OFF_PEAK_END, '0.5');

        self::assertSame('0.500000', $calculator->eventCost($tokens, $row, $offPeak, 18 * 60)); // 18:00 — в окне
        self::assertSame('0.500000', $calculator->eventCost($tokens, $row, $offPeak, 15));      // 00:15 — окно через полночь
        self::assertSame('1.000000', $calculator->eventCost($tokens, $row, $offPeak, 30));      // 00:30 — граница (exclusive)
        self::assertSame('1.000000', $calculator->eventCost($tokens, $row, null, 18 * 60));     // без политики — пик
    }

    public function testRoundingIsHalfUpPerEvent(): void
    {
        $calculator = new RateCalculator();
        $row = $this->row(PriceUnit::Per1mTokens, [
            'input' => '1.000001',
            'output' => '0.000000',
            'cache_read' => '0.000000',
            'cache_write' => '0.000000',
        ]);
        $tokens = new TokenCounts(500000, 0, 0, 0); // 0.5000005 → HALF_UP до 6 знаков → 0.500001

        self::assertSame('0.500001', $calculator->eventCost($tokens, $row, null, 60));
    }
}
