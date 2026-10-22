<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testRoundHalfUpExampleFromTask(): void
    {
        self::assertSame('14.262499', Money::roundHalfUp('14.26249875', 6));
    }

    public function testRoundHalfUpClassicHalfUp(): void
    {
        self::assertSame('3', Money::roundHalfUp('2.5', 0));
        self::assertSame('2', Money::roundHalfUp('2.4999', 0));
        self::assertSame('1.01', Money::roundHalfUp('1.005', 2));
        self::assertSame('1.00', Money::roundHalfUp('1.004999', 2));
    }

    public function testRoundHalfUpTinyValues(): void
    {
        self::assertSame('0.000001', Money::roundHalfUp('0.0000005', 6));
        self::assertSame('0.000000', Money::roundHalfUp('0.0000004999', 6));
    }

    public function testArithmeticIsExactDecimal(): void
    {
        // 0.1 + 0.2 в float даёт 0.30000000000000004; здесь — точное 0.3.
        self::assertSame('0.300000', Money::format(Money::add('0.1', '0.2'), 6));
        self::assertSame('0.300000000000', Money::format(Money::add('0.1', '0.2'), 12));

        self::assertSame('3.000000', Money::format(Money::mul('1.5', '2'), 6));
        self::assertSame('0.100000', Money::format(Money::div('1', '10'), 6));
    }

    public function testFormatPadsToSixDecimals(): void
    {
        self::assertSame('8.400000', Money::format('8.4'));
        self::assertSame('0.001000', Money::format('0.001'));
    }
}
