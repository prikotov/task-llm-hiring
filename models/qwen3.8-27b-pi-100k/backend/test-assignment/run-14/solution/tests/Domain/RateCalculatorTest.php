<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Pricing\Money;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PricedModel;
use App\Domain\Pricing\PricingScheme;
use App\Domain\Pricing\RateCalculator;
use App\Domain\Usage\TokenBreakdown;
use PHPUnit\Framework\TestCase;

final class RateCalculatorTest extends TestCase
{
    public function testPerTokenExampleE1(): void
    {
        $priced = new PricedModel(
            PricingScheme::PerToken,
            [
                'input' => '3.000000',
                'output' => '12.000000',
                'cache_read' => '0.300000',
                'cache_write' => '3.750000',
            ],
        );
        $calc = new RateCalculator(null, $priced);
        $tokens = new TokenBreakdown(1200000, 800000, 2500000, 83333);
        $cost = $calc->price($tokens, '11:00');
        $this->assertSame('14.262499', $cost->format());
    }

    public function testUnifiedExampleE3(): void
    {
        $priced = new PricedModel(
            PricingScheme::Unified,
            ['any' => '2.400000'],
        );
        $calc = new RateCalculator(null, $priced);
        $tokens = new TokenBreakdown(3000000, 500000, 0, 0);
        $cost = $calc->price($tokens, '17:10');
        $this->assertSame('8.400000', $cost->format());
    }

    public function testOffPeakMultiplier(): void
    {
        $priced = new PricedModel(
            PricingScheme::Unified,
            ['any' => '2.400000'],
        );
        $offPeak = new OffPeakWindow('00:00', '23:59', '0.5');
        $calc = new RateCalculator($offPeak, $priced);
        $tokens = new TokenBreakdown(1000000, 0, 0, 0);
        // 2.4 * 0.5 = 1.2
        $this->assertSame('1.200000', $calc->price($tokens, '12:00')->format());
    }

    public function testOffPeakWindowCrossMidnight(): void
    {
        $win = new OffPeakWindow('16:30', '00:30', '0.5');
        $this->assertTrue($win->contains('16:30'));
        $this->assertTrue($win->contains('23:59'));
        $this->assertTrue($win->contains('00:29'));
        $this->assertFalse($win->contains('00:30'));
        $this->assertFalse($win->contains('16:29'));
    }

    public function testZeroTokensCost(): void
    {
        $priced = new PricedModel(
            PricingScheme::PerToken,
            ['input' => '1', 'output' => '2', 'cache_read' => '3', 'cache_write' => '4'],
        );
        $calc = new RateCalculator(null, $priced);
        $this->assertTrue($calc->price(new TokenBreakdown(0, 0, 0, 0), '12:00')->isZero());
    }
}
