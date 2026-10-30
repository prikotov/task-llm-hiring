<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Billing\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testRoundTo6IsHalfUp(): void
    {
        self::assertSame('14.262499', Money::of('14.26249875')->roundTo6()->amount());
        self::assertSame('2.400000', Money::of('2.4')->roundTo6()->amount());
    }

    public function testScaleByOffPeakMultiplier(): void
    {
        self::assertSame('0.500000', Money::of('1.0')->scaleBy('0.5')->roundTo6()->amount());
    }

    public function testPlusMinusIsExactDecimal(): void
    {
        self::assertSame('0.300000', Money::of('0.1')->plus(Money::of('0.2'))->format6());
        self::assertSame('0.000000', Money::of('2.5')->minus(Money::of('2.5'))->format6());
    }

    public function testZeroAndString(): void
    {
        self::assertSame('0', Money::zero()->amount());
        self::assertSame('8.4', (string) Money::of('8.4'));
    }
}
