<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Common\Decimal;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testAdditionIsExact(): void
    {
        $sum = Decimal::of('0.1')->add(Decimal::of('0.2'));
        self::assertTrue($sum->equals(Decimal::of('0.3')), '0.1 + 0.2 = 0.3 без потерь float');
    }

    public function testMultiplicationKeepsPrecision(): void
    {
        $value = Decimal::of('83333')->mul(Decimal::of('3.750000'));
        self::assertTrue($value->equals(Decimal::of('312498.75')));
    }

    public function testDivisionWithScale(): void
    {
        self::assertSame('0.3333333333333333', Decimal::of('1')->div(Decimal::of('3'))->value());
    }

    public function testRoundHalfUpRoundsAwayFromZeroOnExactHalf(): void
    {
        self::assertSame('3', Decimal::of('2.5')->roundHalfUp(0)->value());
        self::assertSame('-3', Decimal::of('-2.5')->roundHalfUp(0)->value());
        self::assertSame('1.260001', Decimal::of('1.2600005')->roundHalfUp(6)->value());
        self::assertSame('1.260000', Decimal::of('1.2600004999')->roundHalfUp(6)->value());
    }

    public function testFormatPadsToScale(): void
    {
        self::assertSame('11.000000', Decimal::of('11')->format(6));
        self::assertSame('0.000000', Decimal::zero()->format(6));
        self::assertSame('-1.200000', Decimal::of('-1.2')->format(6));
    }

    public function testSubtractionAndNegation(): void
    {
        $delta = Decimal::of('13.062499')->sub(Decimal::of('14.262499'));
        self::assertSame('-1.200000', $delta->format(6));
        self::assertTrue($delta->isNegative());
    }
}
