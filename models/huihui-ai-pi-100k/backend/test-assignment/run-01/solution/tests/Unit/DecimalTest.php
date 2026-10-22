<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Decimal;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testAddSub(): void
    {
        self::assertTrue(Decimal::of('1.0')->add('2.14')->equals('3.14'));
        self::assertTrue(Decimal::of('10.5')->sub('0.25')->equals('10.25'));
        self::assertTrue(Decimal::of('10.25')->sub('12.75')->equals('-2.5'));
        self::assertTrue(Decimal::zero()->add(0)->isZero());
    }

    public function testMulDivExactDecimalArithmetic(): void
    {
        // 3.75 * 83333 / 1 000 000 — пример из задания (cache_write lumen-8)
        $exact = Decimal::of('3.75')->mul('83333')->div('1000000');
        self::assertTrue($exact->equals('0.31249875'));
        self::assertSame('0.3124987500', $exact->format(10));
        self::assertTrue(Decimal::of('3.75')->mul('83333')->equals('312498.75'));
    }

    public function testRoundHalfUp(): void
    {
        self::assertSame('0.312499', Decimal::of('0.31249875')->roundHalfUp(6)->value());
        self::assertSame('14.262499', Decimal::of('14.26249875')->roundHalfUp(6)->value());
        self::assertSame('2.000001', Decimal::of('2.0000005')->roundHalfUp(6)->value());
        self::assertSame('2.000000', Decimal::of('2.0000004')->roundHalfUp(6)->value());
        self::assertSame('11.000000', Decimal::of('11')->roundHalfUp(6)->value());
        self::assertSame('8.400000', Decimal::of('8.4')->roundHalfUp(6)->value());
    }

    public function testRoundHalfUpNegativeMovesAwayFromZero(): void
    {
        self::assertSame('-3', Decimal::of('-2.5')->roundHalfUp(0)->value());
        self::assertSame('-2', Decimal::of('-2.4')->roundHalfUp(0)->value());
    }

    public function testEqualsAndSign(): void
    {
        self::assertTrue(Decimal::of('0.50')->equals('0.5'));
        self::assertFalse(Decimal::of('0.5')->equals('0.501'));
        self::assertTrue(Decimal::of('-1')->isNegative());
        self::assertFalse(Decimal::of('1')->isNegative());
        self::assertFalse(Decimal::of('0.000001')->isZero());
    }

    public function testRequiredAndDivisionByZero(): void
    {
        $this->expectException(\DomainException::class);
        Decimal::required(null, 'input');
    }

    public function testDivisionByZeroThrows(): void
    {
        $this->expectException(\DomainException::class);
        Decimal::of('5')->div('0');
    }

    public function testFormatPadsToExactPlaces(): void
    {
        self::assertSame('0.500000', Decimal::of('0.5')->format(6));
        self::assertSame('12.345679', Decimal::of('12.345679')->format(6));
        self::assertSame('12.345678', Decimal::of('12.345678999')->format(6)); // обрыв без округления
    }
}
