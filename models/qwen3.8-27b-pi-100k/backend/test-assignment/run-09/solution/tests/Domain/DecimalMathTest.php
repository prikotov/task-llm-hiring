<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Billing\DecimalMath;
use PHPUnit\Framework\TestCase;

class DecimalMathTest extends TestCase
{
    public function testHalfUpSixRoundsHalfAwayFromZero(): void
    {
        self::assertSame('2.500000', DecimalMath::halfUp6('2.5'));
        // 7-й знак 7 -> округляем вверх: 14.262498|75 -> 14.262499
        self::assertSame('14.262499', DecimalMath::halfUp6('14.26249875'));
        // ровно половина 5 -> от нуля: 0.123456|5 -> 0.123457
        self::assertSame('0.123457', DecimalMath::halfUp6('0.1234565'));
        // перенос с разряда: 0.999999|9 -> 1.000000
        self::assertSame('1.000000', DecimalMath::halfUp6('0.9999999'));
        self::assertSame('11.000000', DecimalMath::halfUp6('11'));
    }

    public function testHalfUpSixWorksForNegativeValues(): void
    {
        self::assertSame('-0.123457', DecimalMath::halfUp6('-0.1234565'));
        self::assertSame('-14.262499', DecimalMath::halfUp6('-14.26249875'));
    }

    public function testArithmeticIsExactWithoutFloats(): void
    {
        // bcmath: 0.1 + 0.2 точно 0.3 (в отличие от float, где 0.30000000000000004)
        self::assertSame('0.300000', DecimalMath::format6(DecimalMath::add('0.1', '0.2')));
        self::assertSame('2.400000', DecimalMath::format6(DecimalMath::mul('2.4', '1')));
        self::assertSame('3.600000', DecimalMath::format6(DecimalMath::mul('1.2', '3.0')));
        self::assertSame('0.000024', DecimalMath::format6(DecimalMath::div('24', '1000000')));
        self::assertSame('9.600000', DecimalMath::format6(DecimalMath::div(DecimalMath::mul('1200000', '8.0'), DecimalMath::TOKEN_DIVISOR)));
    }

    public function testFormat6PadsToSixPlaces(): void
    {
        self::assertSame('12.000000', DecimalMath::format6('12'));
        self::assertSame('0.500000', DecimalMath::format6('0.5'));
        self::assertSame('8.400000', DecimalMath::format6('8.4'));
        self::assertSame('-3.100000', DecimalMath::format6('-3.10'));
    }
}
