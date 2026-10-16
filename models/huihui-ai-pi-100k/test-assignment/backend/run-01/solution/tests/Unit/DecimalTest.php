<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Decimal;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testMulDivExact(): void
    {
        self::assertSame('1.5' . str_repeat('0', 19), Decimal::mul('3', '0.5'));
        self::assertSame('0.33333333333333333333', Decimal::div('1', '3'));
    }

    public function testRoundHalfUp6(): void
    {
        self::assertSame('14.262499', Decimal::roundHalfUp6('14.2624987'));
        self::assertSame('1.000001', Decimal::roundHalfUp6('1.0000005'));
        self::assertSame('1.000000', Decimal::roundHalfUp6('0.9999995'));
        self::assertSame('1.500000', Decimal::roundHalfUp6('1.5'));
        self::assertSame('0.000500', Decimal::roundHalfUp6('0.00050025'));
        // ровно «половина» всегда вверх
        self::assertSame('0.000001', Decimal::roundHalfUp6('0.0000005'));
        self::assertSame('0.000000', Decimal::roundHalfUp6('0.0000004999'));
    }

    public function testMoneyArithmetic(): void
    {
        self::assertSame('1.500000', Decimal::addMoney('1.250000', '0.250000'));
        self::assertSame('-1.000000', Decimal::subMoney('1.000000', '2.000000'));
        self::assertSame('3.000000', Decimal::canonicalMoney('3'));
        self::assertSame('0.000000', Decimal::canonicalMoney('0'));
    }
}
