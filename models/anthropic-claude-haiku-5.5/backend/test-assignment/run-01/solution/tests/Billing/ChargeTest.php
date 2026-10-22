<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\Domain\Catalog\Model;
use App\Billing\Domain\Catalog\OffPeakWindow;
use App\Billing\Domain\Catalog\PricingUnit;
use App\Billing\Domain\Catalog\Price;
use App\Billing\Domain\Catalog\Provider;
use App\Billing\Domain\Shared\Decimal;
use App\Billing\Domain\Shared\Time;
use App\Billing\Domain\Usage\TokenUsage;
use PHPUnit\Framework\TestCase;

final class ChargeTest extends TestCase
{
    public function testPerTokenExampleFromTask(): void
    {
        $price = $this->price(
            PricingUnit::PER_1M_TOKENS,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            new OffPeakWindow('16:30', '00:30', '0.5'),
        );
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);

        // 11:00 — пик: 14.26249875 → HALF_UP → 14.262499
        self::assertSame('14.262499', $price->charge($usage, Time::parseIso('2026-09-15T11:00:00Z')));
    }

    public function testOffPeakWindowCrossesMidnightAndHalfOpen(): void
    {
        $price = $this->price(
            PricingUnit::PER_1M_TOKENS,
            ['input' => '2.000000', 'output' => '0.000000', 'cache_read' => '0.000000', 'cache_write' => '0.000000'],
            new OffPeakWindow('16:30', '00:30', '0.5'),
        );
        $usage = new TokenUsage(1000000, 0, 0, 0);

        self::assertSame('1.000000', $price->charge($usage, Time::parseIso('2026-09-15T23:59:00Z')), 'внутри окна');
        self::assertSame('1.000000', $price->charge($usage, Time::parseIso('2026-09-16T00:29:59Z')), 'внутри окна');
        self::assertSame('2.000000', $price->charge($usage, Time::parseIso('2026-09-16T00:30:00Z')), 'конец исключён');
        self::assertSame('2.000000', $price->charge($usage, Time::parseIso('2026-09-15T16:29:59Z')), 'до начала');
        self::assertSame('1.000000', $price->charge($usage, Time::parseIso('2026-09-15T16:30:00Z')), 'начало включено');
    }

    public function testUnifiedRate(): void
    {
        $price = $this->price(PricingUnit::PER_1M_TOKENS_UNIFIED, ['any' => '2.400000'], null);
        $usage = new TokenUsage(3000000, 500000, 0, 0);

        self::assertSame('8.400000', $price->charge($usage, Time::parseIso('2026-09-20T17:10:00Z')));
    }

    public function testZeroUsageCostsZero(): void
    {
        $price = $this->price(PricingUnit::PER_1M_TOKENS_UNIFIED, ['any' => '2.400000'], null);

        self::assertSame('0.000000', $price->charge(new TokenUsage(0, 0, 0, 0), Time::parseIso('2026-09-20T17:10:00Z')));
    }

    public function testRoundingIsHalfUpOnEventLevel(): void
    {
        self::assertSame('0.000001', Decimal::roundEvent('0.0000005'));
        self::assertSame('0.000000', Decimal::roundEvent('0.0000004'));
        self::assertSame('2.500000', Decimal::roundEvent('2.5'));
        self::assertSame('-0.000001', Decimal::roundEvent('-0.0000005'));
    }

    /** @param array<string, string> $rates */
    private function price(PricingUnit $unit, array $rates, ?OffPeakWindow $offPeak): Price
    {
        $provider = new Provider('p', 'P', $offPeak);
        $model = new Model($provider, 'm');

        return new Price($model, Time::parseIso('2026-08-01T00:00:00Z'), 'USD', $unit, $rates);
    }
}
