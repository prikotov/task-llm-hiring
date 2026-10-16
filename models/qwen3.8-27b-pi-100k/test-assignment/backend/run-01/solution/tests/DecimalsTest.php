<?php

declare(strict_types=1);

namespace App\Tests;

use App\Support\Decimals;
use PHPUnit\Framework\TestCase;

final class DecimalsTest extends TestCase
{
    public function testNormalizeStripsLeadingZerosAndTrailingZeros(): void
    {
        self::assertSame('3', Decimals::normalize('3.000000'));
        self::assertSame('0.5', Decimals::normalize('0.50'));
        self::assertSame('7', Decimals::normalize('007.000'));
        self::assertSame('0.07', Decimals::normalize('000.070'));
    }

    public function testAssertDecimalRejectsInvalid(): void
    {
        foreach (['', 'abc', '-1', '1e3', '1.', '.', '-0.5'] as $bad) {
            $this->expectException(\InvalidArgumentException::class);
            Decimals::assertDecimal($bad);
        }
    }

    public function testBasicArithmeticIsExact(): void
    {
        self::assertSame('0.3', rtrim(Decimals::add('0.1', '0.2'), '0') === '0.3' ? '0.3' : Decimals::add('0.1', '0.2'));
        self::assertTrue(bccomp(Decimals::add('0.1', '0.2'), '0.3', 20) === 0, '0,1 + 0,2 = 0,3');
        self::assertTrue(bccomp(Decimals::sub('0.3', '0.1'), '0.2', 20) === 0);
        self::assertTrue(bccomp(Decimals::mul('3', '0.5'), '1.5', 20) === 0);
        self::assertSame('0.33333333333333333333', Decimals::div('1', '3'));
    }

    public function testRoundHalfUp6(): void
    {
        self::assertSame('14.262499', Decimals::roundHalfUp6('14.26249875'));
        self::assertSame('0.000001', Decimals::roundHalfUp6('0.0000005'));
        self::assertSame('0.000000', Decimals::roundHalfUp6('0.0000004'));
        self::assertSame('2.700000', Decimals::roundHalfUp6('2.6999995'));
        self::assertSame('2.699999', Decimals::roundHalfUp6('2.6999994'));
        self::assertSame('0.012000', Decimals::roundHalfUp6('0.012'));
        self::assertSame('0.000000', Decimals::roundHalfUp6('0'));
        self::assertSame('11.000000', Decimals::roundHalfUp6('11'));
        self::assertSame('7.131249', Decimals::roundHalfUp6('7.131249375'));
    }

    public function testFormat6AlwaysSixDecimals(): void
    {
        self::assertSame('12.000000', Decimals::format6('12'));
        self::assertSame('0.123456', Decimals::format6('0.123456'));
        self::assertSame('0.000000', Decimals::format6('0'));
        self::assertSame('-0.929166', Decimals::format6('-0.929166'));
        self::assertSame('0.929166', Decimals::format6('0.929166'));
    }
}
