<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\CostCalculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    public function testPerTokenRatesUseExactArithmeticAndRoundOnceHalfUp(): void
    {
        $event = [
            'occurred_at' => new DateTimeImmutable('2026-09-15T11:00:00Z'),
            'tokens_input' => 1_200_000,
            'tokens_output' => 800_000,
            'tokens_cache_read' => 2_500_000,
            'tokens_cache_write' => 83_333,
        ];
        $price = [
            'unit' => 'per_1m_tokens',
            'rate_input' => '3.000000',
            'rate_output' => '12.000000',
            'rate_cache_read' => '0.300000',
            'rate_cache_write' => '3.750000',
        ];
        $provider = ['off_peak_start' => '16:30', 'off_peak_end' => '00:30', 'off_peak_multiplier' => '0.5'];

        self::assertSame('14.262499', (new CostCalculator())->calculate($event, $price, $provider));
    }

    public function testUnifiedRatesAndMidnightCrossingOffPeakWindow(): void
    {
        $event = [
            'occurred_at' => new DateTimeImmutable('2026-09-15T17:10:00Z'),
            'tokens_input' => 3_000_000,
            'tokens_output' => 500_000,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];
        $price = ['unit' => 'per_1m_tokens_unified', 'rate_any' => '2.400000'];
        $provider = ['off_peak_start' => '16:30', 'off_peak_end' => '00:30', 'off_peak_multiplier' => '0.5'];

        self::assertSame('4.200000', (new CostCalculator())->calculate($event, $price, $provider));
    }

    public function testOffPeakWindowEndIsExclusive(): void
    {
        $event = [
            'occurred_at' => new DateTimeImmutable('2026-09-15T00:30:00Z'),
            'tokens_input' => 1_000_000,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];
        $price = [
            'unit' => 'per_1m_tokens',
            'rate_input' => '1.000000',
            'rate_output' => '0.000000',
            'rate_cache_read' => '0.000000',
            'rate_cache_write' => '0.000000',
        ];
        $provider = ['off_peak_start' => '16:30', 'off_peak_end' => '00:30', 'off_peak_multiplier' => '0.5'];

        self::assertSame('1.000000', (new CostCalculator())->calculate($event, $price, $provider));
    }
}
