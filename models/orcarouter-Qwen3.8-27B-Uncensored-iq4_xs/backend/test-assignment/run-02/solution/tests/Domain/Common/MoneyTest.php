<?php

declare(strict_types=1);

namespace App\Tests\Domain\Common;

use App\Domain\Common\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[Test]
    public function formatsExactlySixFractionDigits(): void
    {
        self::assertSame('14.262499', Money::format('14.262499'));
        self::assertSame('14.200000', Money::format('14.2'));
        self::assertSame('0.000000', Money::format('0'));
    }

    #[Test]
    public function roundsHalfUpAndTruncatesTail(): void
    {
        self::assertSame('14.262499', Money::roundHalfUp('14.26249875'));
        self::assertSame('14.262500', Money::roundHalfUp('14.2624995'));
        self::assertSame('14.262500', Money::roundHalfUp('14.2625004999'));
        self::assertSame('2.700000', Money::roundHalfUp('2.7000001'));
        self::assertSame('33.662499', Money::roundHalfUp('33.6624985'));
    }

    #[Test]
    public function moneyArithmeticIsExact(): void
    {
        self::assertSame(0, bccomp(Money::add('0.1', '0.2'), '0.3'));
        self::assertSame(0, bccomp(Money::sub('0.1', '0.1'), '0'));
        self::assertSame(0, bccomp(Money::add('1.500000', '1.200000'), '2.700000'));
    }

    #[Test]
    public function handlesLongValuesWithoutPrecisionLoss(): void
    {
        self::assertSame('123456789012345678.123457', Money::roundHalfUp('123456789012345678.123456789'));
    }
}
