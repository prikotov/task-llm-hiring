<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Common\Iso8601Utc;
use App\Domain\Pricing\CostCalculator;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceRowValue;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\TokenKind;
use App\Domain\Pricing\TokenUsage;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private function perTokenRow(array $rates): PriceRowValue
    {
        return new PriceRowValue(
            providerCode: 'prism',
            modelCode: 'lumen-8',
            validFrom: Iso8601Utc::parse('2026-08-01T00:00:00Z'),
            currency: 'USD',
            unit: PriceUnit::Per1mTokens,
            rates: $rates,
        );
    }

    public function testFixtureEventE1PerToken(): void
    {
        // 1.2M × 3.0 + 0.8M × 12.0 + 2.5M × 0.3 + 83333 × 3.75 / 1M = 14.26249875 → 14.262499 (HALF_UP)
        $row = $this->perTokenRow([
            TokenKind::Input->value => '3.000000',
            TokenKind::Output->value => '12.000000',
            TokenKind::CacheRead->value => '0.300000',
            TokenKind::CacheWrite->value => '3.750000',
        ]);
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);

        $cost = (new CostCalculator())->eventCost($usage, $row, Iso8601Utc::parse('2026-09-15T11:00:00Z'));

        self::assertSame('14.262499', $cost);
    }

    public function testOffPeakMultiplierHoldingOtherKindsZero(): void
    {
        // 1.2M × 3.0 = 3.60 → ×0.5 (16:30–00:30) = 1.800000; вне окна — 3.600000
        $row = $this->perTokenRow([
            TokenKind::Input->value => '3.000000',
            TokenKind::Output->value => '0.0',
            TokenKind::CacheRead->value => '0.0',
            TokenKind::CacheWrite->value => '0.0',
        ]);
        $usage = new TokenUsage(1200000, 0, 0, 0);
        $window = new OffPeakWindow(990, 30, '0.5');

        self::assertSame('1.800000', (new CostCalculator())->eventCost($usage, $row, Iso8601Utc::parse('2026-09-16T17:00:00Z'), $window));
        self::assertSame('3.600000', (new CostCalculator())->eventCost($usage, $row, Iso8601Utc::parse('2026-09-16T10:00:00Z'), $window));
    }

    public function testUnifiedRow(): void
    {
        // (3M + 0.5M) × 2.4 / 1M = 8.4 → 8.400000
        $row = new PriceRowValue(
            providerCode: 'sfera',
            modelCode: 'gyro-pro',
            validFrom: Iso8601Utc::parse('2026-07-01T00:00:00Z'),
            currency: 'USD',
            unit: PriceUnit::Per1mTokensUnified,
            rates: [TokenKind::Any->value => '2.400000'],
        );
        $usage = new TokenUsage(3000000, 500000, 0, 0);

        $cost = (new CostCalculator())->eventCost($usage, $row, Iso8601Utc::parse('2026-09-20T17:10:00Z'), null);

        self::assertSame('8.400000', $cost);
    }

    public function testRoundingHappensOnceAtEventLevel(): void
    {
        // 1M × 0.0000005 + 1M × 0.0000005 = 0.000001;
        // если бы округление делалось по видам, вышло бы 0.000002.
        $row = $this->perTokenRow([
            TokenKind::Input->value => '0.0000005',
            TokenKind::Output->value => '0.0000005',
            TokenKind::CacheRead->value => '0.0',
            TokenKind::CacheWrite->value => '0.0',
        ]);
        $usage = new TokenUsage(1000000, 1000000, 0, 0);

        $cost = (new CostCalculator())->eventCost($usage, $row, Iso8601Utc::parse('2026-09-15T11:00:00Z'));

        self::assertSame('0.000001', $cost);
    }

    public function testZeroTokensCostZero(): void
    {
        $row = $this->perTokenRow([
            TokenKind::Input->value => '3.0',
            TokenKind::Output->value => '12.0',
            TokenKind::CacheRead->value => '0.3',
            TokenKind::CacheWrite->value => '3.75',
        ]);
        $usage = new TokenUsage(0, 0, 0, 0);

        $cost = (new CostCalculator())->eventCost($usage, $row, Iso8601Utc::parse('2026-09-15T11:00:00Z'));

        self::assertSame('0.000000', $cost);
    }
}
