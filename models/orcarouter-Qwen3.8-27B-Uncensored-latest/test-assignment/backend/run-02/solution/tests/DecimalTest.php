<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Decimal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    #[Test]
    public function roundHalfUpIsHalfAwayFromZero(): void
    {
        self::assertSame('14.262499', Decimal::roundHalfUp('14.26249875', 6));
        self::assertSame('3', Decimal::roundHalfUp('2.5', 0));
        self::assertSame('2.5', Decimal::roundHalfUp('2.5', 1));
        self::assertSame('-3', Decimal::roundHalfUp('-2.5', 0));
        self::assertSame('1.000000', Decimal::roundHalfUp('1', 6));
        self::assertSame('0.000000', Decimal::roundHalfUp('0.0000004', 6));
        self::assertSame('0.000001', Decimal::roundHalfUp('0.0000005', 6));
    }

    #[Test]
    public function arithmeticIsExact(): void
    {
        self::assertSame('0.30', Decimal::format(Decimal::add('0.1', '0.2'), 2));
        self::assertSame('3.60', Decimal::format(Decimal::div(Decimal::mul('1200000', '3.0'), '1000000'), 2));
        self::assertSame('8.400000', Decimal::format(Decimal::div(Decimal::mul('3500000', '2.4'), '1000000'), 6));
        self::assertSame('33.662499', Decimal::format(
            Decimal::add(Decimal::add('14.262499', '8.400000'), '11.000000'),
            6,
        ));
        self::assertSame('2.500000', Decimal::format('2.5', 6));
        self::assertSame('-5.000000', Decimal::format('-5', 6));
    }
}
