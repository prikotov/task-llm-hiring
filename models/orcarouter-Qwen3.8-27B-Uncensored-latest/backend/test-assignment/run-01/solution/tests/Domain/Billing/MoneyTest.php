<?php

declare(strict_types=1);

namespace Tests\Domain\Billing;

use App\Domain\Billing\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testAddSubMulKeepDecimalsWithoutFloats(): void
    {
        self::assertSame(bccomp(Money::add('1.100000', '2.200000'), '3.300000', 6), 0);
        self::assertMatchesRegularExpression('/^-?[\d,]*\d(\.\d{0,20})?$/', Money::mul('14.26249875', '0.5'));
        self::assertSame(bccomp(Money::sub('5.000000', '5.200000'), '-0.200000', 6), 0);
    }

    public function testPerMillionIsExact(): void
    {
        // 83333 * 3.75 / 1e6 = 0.31249875 — float потерял бы хвост.
        self::assertSame(bccomp(Money::perMillion(83_333, '3.750000'), '0.31249875', 8), 0);
    }

    public function testRoundHalfUpTo6(): void
    {
        self::assertSame('14.262499', Money::roundHalfUpTo6('14.26249875'));
        self::assertSame('12.062499', Money::roundHalfUpTo6('12.06249875'));
        self::assertSame('11.000000', Money::roundHalfUpTo6('11'));
        self::assertSame('0.000000', Money::roundHalfUpTo6('0.0000001'));
        self::assertSame('0.000001', Money::roundHalfUpTo6('0.0000005'));
        self::assertSame('1.000001', Money::roundHalfUpTo6('1.0000005'));
        self::assertSame('0.123457', Money::roundHalfUpTo6('0.1234565'));
    }

    public function testFormat6PadsToSixDecimals(): void
    {
        self::assertSame('8.400000', Money::format6('8.4'));
        self::assertSame('-2.200000', Money::format6('-2.2'));
        self::assertSame('0.000000', Money::format6('0'));
    }

    public function testRoundRejectsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::roundHalfUpTo6('-0.1');
    }
}
