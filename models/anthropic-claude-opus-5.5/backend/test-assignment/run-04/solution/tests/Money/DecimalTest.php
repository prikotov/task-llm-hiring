<?php

declare(strict_types=1);

namespace App\Tests\Money;

use App\Domain\Money\Decimal;
use App\Domain\Money\Exception\InvalidAmount;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testAdditionAndMultiplicationKeepPrecision(): void
    {
        $cost = Decimal::fromString('14.26249875');

        self::assertSame('14.262499', $cost->roundHalfUp(6)->fixed(6));
    }

    public function testHalfUpRoundsAwayFromZero(): void
    {
        self::assertSame('3', Decimal::fromString('2.5')->roundHalfUp(0)->fixed(0));
        self::assertSame('-3', Decimal::fromString('-2.5')->roundHalfUp(0)->fixed(0));
        self::assertSame('0.000001', Decimal::fromString('0.0000005')->roundHalfUp(6)->fixed(6));
        self::assertSame('0.000000', Decimal::fromString('0.0000004')->roundHalfUp(6)->fixed(6));
    }

    public function testFloatLikeInputIsNeverUsedButStringIsExact(): void
    {
        // 0.1 + 0.2 в float дало бы 0.30000000000000004.
        $sum = Decimal::fromString('0.1')->add(Decimal::fromString('0.2'));

        self::assertSame('0.3', (string) $sum);
    }

    public function testDivisionByZeroIsAnError(): void
    {
        $this->expectException(InvalidAmount::class);

        Decimal::fromString('1')->div(Decimal::zero());
    }

    public function testRejectsGarbage(): void
    {
        $this->expectException(InvalidAmount::class);

        Decimal::fromString('3,5');
    }

    public function testFixedPadsTrailingZeros(): void
    {
        self::assertSame('8.400000', Decimal::fromString('8.4')->fixed(6));
        self::assertSame('-0.500000', Decimal::fromString('-0.5')->fixed(6));
    }

    public function testSumOfRoundedValuesIsNotRerounded(): void
    {
        $sum = Decimal::sum([
            Decimal::fromString('14.262499'),
            Decimal::fromString('8.400000'),
            Decimal::fromString('11.000000'),
        ]);

        self::assertSame('33.662499', $sum->fixed(6));
    }
}
