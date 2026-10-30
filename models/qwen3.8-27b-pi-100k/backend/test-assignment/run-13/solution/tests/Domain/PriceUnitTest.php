<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use App\Domain\PricingSchemaException;
use App\Domain\PriceUnit;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PriceUnitTest extends TestCase
{
    #[Test]
    public function testPer1mTokensCost(): void
    {
        // Input 1200000, output 800000, cache_read 2500000, cache_write 83333
        $rates = [
            'input' => '3',
            'output' => '12',
            'cache_read' => '0.3',
            'cache_write' => '3.75',
        ];

        $cost = PriceUnit::cost(
            PriceUnit::PER_1M_TOKENS,
            $rates,
            1200000,
            800000,
            2500000,
            83333,
        );

        self::assertSame('14.26249875', Money::format(Money::round($cost, 9)));
    }

    #[Test]
    public function testUnifiedCost(): void
    {
        // 3000000 input + 500000 output at 2.4 per 1M
        $rates = ['any' => '2.4'];
        $cost = PriceUnit::cost(
            PriceUnit::PER_1M_TOKENS_UNIFIED,
            $rates,
            3000000,
            500000,
            0,
            0,
        );

        self::assertSame('8.4', \App\Domain\Money::format($cost));
    }

    #[Test]
    public function testNormalizeRatesPer1M(): void
    {
        $rates = PriceUnit::normalizeRates(PriceUnit::PER_1M_TOKENS, [
            'input' => '3.00',
            'output' => '12.00',
            'cache_read' => '0.30',
            'cache_write' => '3.75',
        ]);
        self::assertSame(['input' => '3', 'output' => '12', 'cache_read' => '0.3', 'cache_write' => '3.75'], $rates);
    }

    #[Test]
    public function testNormalizeRatesUnified(): void
    {
        $rates = PriceUnit::normalizeRates(PriceUnit::PER_1M_TOKENS_UNIFIED, ['any' => '2.40']);
        self::assertSame(['any' => '2.4'], $rates);
    }

    #[Test]
    public function testNormalizeRatesMissingRate(): void
    {
        $this->expectException(PricingSchemaException::class);
        PriceUnit::normalizeRates(PriceUnit::PER_1M_TOKENS, ['input' => '1']);
    }

    #[Test]
    public function testUnknownUnit(): void
    {
        $this->expectException(PricingSchemaException::class);
        PriceUnit::cost('unknown_unit', [], 0, 0, 0, 0);
    }

    #[Test]
    public function testNewPriceAfterOld(): void
    {
        // 2000000 input at 1.5/new price = 3.0
        $rates = ['input' => '1.5', 'output' => '8', 'cache_read' => '0.15', 'cache_write' => '1.875'];
        $cost = PriceUnit::cost(
            PriceUnit::PER_1M_TOKENS,
            $rates,
            2000000,
            1000000,
            0,
            0,
        );

        // = (2000000*1.5 + 1000000*8) / 1e6 = (3000000 + 8000000) / 1e6 = 11
        self::assertSame('11', \App\Domain\Money::format($cost));
    }
}
