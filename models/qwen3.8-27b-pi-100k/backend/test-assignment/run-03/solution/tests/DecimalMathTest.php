<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Billing\DecimalMath;
use PHPUnit\Framework\TestCase;

final class DecimalMathTest extends TestCase
{
    public function testRoundHalfUpRoundUpOnFiveBoundary(): void
    {
        self::assertSame('2.35', DecimalMath::roundHalfUp('2.345', 2));
        self::assertSame('0.000001', DecimalMath::roundHalfUp('0.0000005', 6));
    }

    public function testRoundHalfUpRoundDownBelowBoundary(): void
    {
        self::assertSame('2.34', DecimalMath::roundHalfUp('2.344', 2));
        self::assertSame('0.000000', DecimalMath::roundHalfUp('0.0000004', 6));
    }

    public function testRoundHalfUpIsTowardInfinityForPositives(): void
    {
        self::assertSame('11.000000', DecimalMath::roundHalfUp('10.9999995', 6));
        self::assertSame('14.262499', DecimalMath::roundHalfUp('14.26249875', 6));
    }

    public function testRoundHalfUpNegativeValues(): void
    {
        self::assertSame('-2.35', DecimalMath::roundHalfUp('-2.345', 2));
        self::assertSame('-0.000001', DecimalMath::roundHalfUp('-0.0000005', 6));
    }

    public function testRoundHalfUpAlwaysPrintsExactScale(): void
    {
        self::assertSame('0.000000', DecimalMath::roundHalfUp('0', 6));
        self::assertSame('5.500000', DecimalMath::roundHalfUp('5.5', 6));
        self::assertSame('8.400000', DecimalMath::roundHalfUp('8.4', 6));
    }

    public function testTokenCostIsExactWithoutRounding(): void
    {
        // 1.2M × 3 / 1M = ровно 3.6, без округления
        self::assertSame('3.6000000000000000', DecimalMath::tokenCost(1200000, '3.000000'));
        // 83333 × 3.75 / 1e6
        self::assertSame('0.3124987500000000', DecimalMath::tokenCost(83333, '3.750000'));
        // 3.5M × 2.4 / 1e6
        self::assertSame('8.4000000000000000', DecimalMath::tokenCost(3500000, '2.400000'));
    }

    public function testTokenCostZeroInputs(): void
    {
        self::assertSame('0', DecimalMath::tokenCost(0, '3.000000'));
        self::assertSame('0', DecimalMath::tokenCost(1000, '0.000000'));
    }

    public function testIsZero(): void
    {
        self::assertTrue(DecimalMath::isZero('0'));
        self::assertTrue(DecimalMath::isZero('0.000000'));
        self::assertFalse(DecimalMath::isZero('0.0000001'));
        self::assertFalse(DecimalMath::isZero('-0.1'));
    }
}
