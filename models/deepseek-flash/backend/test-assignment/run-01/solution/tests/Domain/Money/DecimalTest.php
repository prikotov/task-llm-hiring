<?php

declare(strict_types=1);

namespace App\Tests\Domain\Money;

use App\Domain\Money\Decimal;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testParsesAndNormalizesWithoutFloats(): void
    {
        self::assertSame('3', (string) Decimal::fromString('3.000000'));
        self::assertSame('0.000001', (string) Decimal::fromString('+0.0000010'));
        self::assertSame('1200000', (string) Decimal::fromInt(1_200_000));
        self::assertSame('0', (string) Decimal::fromString('-0.000'));
    }

    public function testPerformsExactArithmetic(): void
    {
        $cost = Decimal::fromInt(83_333)
            ->multiply(Decimal::fromString('3.750000'))
            ->divide(Decimal::fromInt(1_000_000));

        self::assertSame('0.31249875', (string) $cost);
    }

    public function testRoundsHalfUpAwayFromZero(): void
    {
        self::assertSame('3', Decimal::fromString('2.5')->roundHalfUp(0)->format(0));
        self::assertSame('-3', Decimal::fromString('-2.5')->roundHalfUp(0)->format(0));
        self::assertSame('2', Decimal::fromString('2.499999')->roundHalfUp(0)->format(0));
        self::assertSame('14.262499', Decimal::fromString('14.26249875')->roundHalfUp(6)->format(6));
        self::assertSame('0.000003', Decimal::fromString('0.0000025')->roundHalfUp(6)->format(6));
    }

    public function testFormatsFixedScale(): void
    {
        self::assertSame('8.400000', Decimal::fromString('8.4')->format(6));
        self::assertSame('0.000000', Decimal::zero()->format(6));
        self::assertSame('1000', Decimal::fromString('1000')->format(0));
    }

    public function testRejectsInvalidValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Decimal::fromString('1.2e3');
    }
}
