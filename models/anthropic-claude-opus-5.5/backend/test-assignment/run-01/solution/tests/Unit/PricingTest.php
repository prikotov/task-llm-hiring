<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Pricing\Domain\LlmModel;
use App\Pricing\Domain\OffPeakWindow;
use App\Pricing\Domain\PriceRow;
use App\Pricing\Domain\PricingUnit;
use App\Pricing\Domain\Provider;
use App\Pricing\Domain\Rates;
use App\Pricing\Domain\TokenUsage;
use App\Shared\Decimal;
use App\Shared\UtcTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PricingTest extends TestCase
{
    public function testPerTokenExactCostAndSingleRounding(): void
    {
        $rates = Rates::fromArray(PricingUnit::PerMillionTokens, ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000']);
        $exact = $rates->exactCost(new TokenUsage(1200000, 800000, 2500000, 83333));
        self::assertTrue($exact->equals(Decimal::of('14.26249875')));
        self::assertSame('14.262499', $exact->toFixed(6));
    }

    public function testUnified(): void
    {
        $rates = Rates::fromArray(PricingUnit::PerMillionTokensUnified, ['any' => '2.400000']);
        self::assertSame('8.400000', $rates->exactCost(new TokenUsage(3000000, 500000, 0, 0))->toFixed(6));
        self::assertSame('0.000000', $rates->exactCost(new TokenUsage(0, 0, 0, 0))->toFixed(6));
    }

    public function testHalfUp(): void
    {
        self::assertSame('0.000003', Decimal::of('0.0000025')->round(6)->toFixed(6));
        self::assertSame('0.000002', Decimal::of('0.0000024999')->round(6)->toFixed(6));
    }

    /** @return iterable<array{string, bool}> */
    public static function offPeakCases(): iterable
    {
        yield ['2026-09-15T16:29:59Z', false];
        yield ['2026-09-15T16:30:00Z', true];
        yield ['2026-09-15T23:59:59Z', true];
        yield ['2026-09-16T00:00:00Z', true];
        yield ['2026-09-16T00:29:59Z', true];
        yield ['2026-09-16T00:30:00Z', false];
        yield ['2026-09-16T11:00:00Z', false];
    }

    #[DataProvider('offPeakCases')]
    public function testOffPeakWindowAcrossMidnight(string $ts, bool $expected): void
    {
        $w = OffPeakWindow::create('16:30', '00:30', '0.5');
        self::assertSame($expected, $w->contains(UtcTime::parse($ts)));
    }

    public function testOffPeakAppliedBeforeRounding(): void
    {
        $provider = new Provider('prism', 'Prism', OffPeakWindow::create('16:30', '00:30', '0.5'));
        $row = new PriceRow(new LlmModel($provider, 'x'), UtcTime::parse('2026-01-01T00:00:00Z'), 'USD', Rates::fromArray(PricingUnit::PerMillionTokensUnified, ['any' => '0.000001']));
        // 5 токенов × 0.000001 / 1M = 5e-12; ×0.5 → 2.5e-12 → 0.000000
        self::assertSame('0.000000', $row->costOf(new TokenUsage(5, 0, 0, 0), UtcTime::parse('2026-01-01T17:00:00Z'))->toFixed(6));
        self::assertSame('7.500000', (new PriceRow(new LlmModel($provider, 'y'), UtcTime::parse('2026-01-01T00:00:00Z'), 'USD', Rates::fromArray(PricingUnit::PerMillionTokensUnified, ['any' => '15'])))
            ->costOf(new TokenUsage(1000000, 0, 0, 0), UtcTime::parse('2026-01-01T17:00:00Z'))->toFixed(6));
    }
}
