<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testRoundHalfUpToSixDigits(): void
    {
        self::assertSame('0.000001', Money::roundHalfUp6('0.0000005'));
        self::assertSame('0.000000', Money::roundHalfUp6('0.0000004999'));
        self::assertSame('1.000003', Money::roundHalfUp6('1.0000025'));
        self::assertSame('0.000002', Money::roundHalfUp6('0.0000015'));
        self::assertSame('14.262499', Money::roundHalfUp6('14.26249875'));
        self::assertSame('0.000000', Money::roundHalfUp6('0'));
        self::assertSame('11.000000', Money::roundHalfUp6('11'));
    }

    public function testAddIsExact(): void
    {
        $sums = Money::add('14.262499', Money::add('8.400000', '11.000000'));

        self::assertSame('33.662499', Money::format6($sums));
    }

    public function testFormatSixDigits(): void
    {
        self::assertSame('14.262499', Money::format6('14.262499'));
        self::assertSame('14.260000', Money::format6('14.26'));
        self::assertSame('0.500000', Money::format6('0.5'));
        self::assertSame('5.000000', Money::format6('5'));
        self::assertSame('-2.500000', Money::format6('-2.5'));
    }

    public function testArithmeticStaysDecimal(): void
    {
        // tokens 83 333 x rate 3.75 per 1M tokens = 0.31249875 → rounded to 0.312499
        $cost = Money::div(Money::mul('83333', '3.75'), '1000000');

        self::assertSame('0.3124987500000000', $cost);
        self::assertSame('0.312499', Money::roundHalfUp6($cost));
    }
}
