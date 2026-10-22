<?php

declare(strict_types=1);

namespace App\Tests\Util;

use App\Util\MoneyCalculator;
use PHPUnit\Framework\TestCase;

final class MoneyCalculatorTest extends TestCase
{
    public function testRoundHalfUpExampleFromTask(): void
    {
        // 1,200,000*1.5 + 800,000*12 + 2,500,000*0.5 + 83,333*3.75 = 14,262,498.75
        // / 1,000,000 = 14.26249875 -> 14.262499 (HALF_UP, 6 знаков)
        self::assertSame('14.262499', MoneyCalculator::roundHalfUp('14.26249875'));
    }

    public function testRoundHalfUpTieRoundsAwayFromZero(): void
    {
        self::assertSame('0.000001', MoneyCalculator::roundHalfUp('0.0000005'));
        self::assertSame('123.456790', MoneyCalculator::roundHalfUp('123.4567895'));
        self::assertSame('1.000000', MoneyCalculator::roundHalfUp('1.0000004'));
        self::assertSame('1.01', MoneyCalculator::roundHalfUp('1.005', 2));
    }

    public function testRoundHalfUpSimpleValues(): void
    {
        self::assertSame('2.000000', MoneyCalculator::roundHalfUp('2'));
        self::assertSame('8.400000', MoneyCalculator::roundHalfUp('8.4'));
        self::assertSame('0.000000', MoneyCalculator::roundHalfUp('0'));
    }

    public function testRoundHalfUpNegative(): void
    {
        // Стоимость события неотрицательна, но сам округление — симметричное
        self::assertSame('-1.01', MoneyCalculator::roundHalfUp('-1.005', 2));
    }

    public function testNormalizeKeepsValue(): void
    {
        self::assertSame('8.400000', MoneyCalculator::normalize('8.4'));
        self::assertSame('0.000000', MoneyCalculator::normalize('0'));
        self::assertSame('17.862500', MoneyCalculator::normalize('17.8625'));
    }

    public function testArithmeticIsExact(): void
    {
        self::assertSame(0, bccomp(MoneyCalculator::add('1.5', '2.25'), '3.75', 10));
        self::assertSame(0, bccomp(MoneyCalculator::multiply('0.1', '0.2'), '0.02', 10));
        self::assertSame(0, bccomp(MoneyCalculator::divide('7.5', '2'), '3.75', 10));
    }

    public function testEqIgnoresTrailingZeros(): void
    {
        self::assertTrue(MoneyCalculator::eq('8.400000', '8.4'));
        self::assertTrue(MoneyCalculator::eq('0.000000', '0'));
        self::assertFalse(MoneyCalculator::eq('8.4', '8.400001'));
    }

    public function testAssertDecimalRejectsGarbage(): void
    {
        self::expectException(\DomainException::class);
        MoneyCalculator::assertDecimal('1a2');
    }
}
