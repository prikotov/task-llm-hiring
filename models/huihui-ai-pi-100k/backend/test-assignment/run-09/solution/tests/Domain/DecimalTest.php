<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Decimal;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testHalfUp6RoundsHalfAwayFromZero(): void
    {
        self::assertSame('0.000001', Decimal::halfUp6('0.0000005'));
        self::assertSame('1.123457', Decimal::halfUp6('1.1234565'));
        self::assertSame('1.123456', Decimal::halfUp6('1.1234564999'));

        $exact = '0.01386898';
        self::assertSame('0.013869', Decimal::halfUp6($exact));
    }

    public function testSumAddsExactly(): void
    {
        self::assertSame('0.300000', Decimal::sum6('0.1', '0.2'));
        self::assertSame('1.128766', Decimal::sum6('0.013869', '0.214857', '0.76973', '0.13031'));
    }

    public function testFormatPadsToSixDecimals(): void
    {
        self::assertSame('0.065268', Decimal::format6('0.0652685'));
        self::assertSame('8.400000', Decimal::format6('8.4'));
    }
}
