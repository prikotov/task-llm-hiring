<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Billing\Pricing\PriceUnit;
use App\Billing\Pricing\PricingCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private array $fixtureRates = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    public function testFixtureEventE1Cost(): void
    {
        // e1: 1.2M in + 0.8M out + 2.5M cache_read + 83.333k cache_write (фикстура TASK.md).
        // Точная стоимость: 3.6 + 9.6 + 0.75 + 0.31249875 = 14.26249875 → 14.262499.
        self::assertSame(
            '14.262499',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokens,
                ['input' => 1_200_000, 'output' => 800_000, 'cache_read' => 2_500_000, 'cache_write' => 83_333],
                $this->fixtureRates,
                null,
            ),
        );
    }

    public function testFixtureEventE2CostWithNewerRow(): void
    {
        // e2: строка 2026-09-15T12:00: event ровно в 12:00 → действует новая строка.
        $rates = ['input' => '1.5', 'output' => '8', 'cache_read' => '0.15', 'cache_write' => '1.875'];
        // 2M×1.5 + 1M×8 = 3 + 8 = 11 → 11.000000.
        self::assertSame(
            '11.000000',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokens,
                ['input' => 2_000_000, 'output' => 1_000_000, 'cache_read' => 0, 'cache_write' => 0],
                $rates,
                null,
            ),
        );
    }

    public function testUnifiedSchemeSumsAllKinds(): void
    {
        // e3: (3.0M + 0.5M) × 2.4 / 1M = 8.4 → 8.400000.
        self::assertSame(
            '8.400000',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokensUnified,
                ['input' => 3_000_000, 'output' => 500_000, 'cache_read' => 0, 'cache_write' => 0],
                ['any' => '2.4'],
                null,
            ),
        );
    }

    public function testOffPeakMultiplierIsAppliedBeforeSingleRounding(): void
    {
        // 14.26249875 × 0.5 = 7.131249375 → 7.131249 (единственное округление итога).
        self::assertSame(
            '7.131249',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokens,
                ['input' => 1_200_000, 'output' => 800_000, 'cache_read' => 2_500_000, 'cache_write' => 83_333],
                $this->fixtureRates,
                '0.500000',
            ),
        );
    }

    public function testHalfUpAtExactHalfMicroUsd(): void
    {
        // 1M × 0.0000015 / 1M = 0.0000015 → ровно 0.5 микро-доллара на границе → вверх.
        self::assertSame(
            '0.000002',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokens,
                ['input' => 1_000_000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
                ['input' => '0.0000015', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
                null,
            ),
        );

        // 0.0000014 → ниже границы → вниз.
        self::assertSame(
            '0.000001',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokens,
                ['input' => 1_000_000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
                ['input' => '0.0000014', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
                null,
            ),
        );
    }

    public function testZeroTokensGiveZeroCost(): void
    {
        self::assertSame(
            '0.000000',
            (new PricingCalculator())->calculateEventCost(
                PriceUnit::Per1mTokens,
                ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
                $this->fixtureRates,
                '0.500000',
            ),
        );
    }

    public function testPer1mSchemeRequiresAllFourKinds(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new PricingCalculator())->calculateEventCost(
            PriceUnit::Per1mTokens,
            ['input' => 1, 'output' => 1, 'cache_read' => 1, 'cache_write' => 0],
            ['input' => '1', 'output' => '1', 'cache_read' => '1'], // нет cache_write
            null,
        );
    }

    public function testUnifiedSchemeRequiresAnyRate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new PricingCalculator())->calculateEventCost(
            PriceUnit::Per1mTokensUnified,
            ['input' => 1, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            ['input' => '1'], // нет «any»
            null,
        );
    }
}
