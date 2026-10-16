<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Billing\Money;
use App\Domain\Exception\BillingException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testRoundHalfUpRoundsHalfAwayFromZero(): void
    {
        self::assertSame('0.000000', Money::roundHalfUp('0.0000004'));
        self::assertSame('0.000001', Money::roundHalfUp('0.0000005'));
        self::assertSame('14.262499', Money::roundHalfUp('14.26249875'));
        self::assertSame('11.000000', Money::roundHalfUp('11'));
        self::assertSame('1.000000', Money::roundHalfUp('0.99999995'));
        self::assertSame('0.500000', Money::roundHalfUp('0.5'));
    }

    public function testRoundHalfUpOnLongRecurringTails(): void
    {
        // 1/3 = 0.333333(3): 7-й знак 3 < 5 -> 0.333333.
        self::assertSame('0.333333', Money::roundHalfUp(Money::div('1', '3')));
        // 1/6 = 0.166666(6): хвост > 0.5e-6 -> 0.166667.
        self::assertSame('0.166667', Money::roundHalfUp(Money::div('1', '6')));
        // 2/3 = 0.666666(6): хвост > 0.5e-6 -> 0.666667.
        // Совмещение с проверкой 1/3 показывает, что округление идёт по цифрам,
        // а не по float-представлению.
        self::assertSame('0.666667', Money::roundHalfUp(Money::div('2', '3')));
    }

    public function testArithmeticIsExactWithoutFloats(): void
    {
        self::assertSame('0.020000000000000000000000', Money::mul('0.1', '0.2'));
        self::assertSame('1.200000000000000000000000', Money::add('1.1', '0.1'));
        self::assertSame('1.000000000000000000000000', Money::sub('1.1', '0.1'));
        self::assertSame('1.500000000000000000000000', Money::div('3', '2'));
    }

    public function testFormatPadsToFixedScaleWithoutRounding(): void
    {
        self::assertSame('0.000000', Money::format('0'));
        self::assertSame('14.262499', Money::format('14.262499'));
        self::assertSame('34.000000', Money::format('34'));
    }

    public function testDivisionByZeroThrows(): void
    {
        $this->expectException(BillingException::class);
        Money::div('1', '0');
    }
}
