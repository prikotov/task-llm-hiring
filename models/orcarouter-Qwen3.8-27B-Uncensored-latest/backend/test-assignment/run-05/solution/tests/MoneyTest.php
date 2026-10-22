<?php

declare(strict_types=1);

namespace App\Tests;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_add_without_rounding(): void
    {
        self::assertSame('14.2624987500000000', Money::add(
            Money::div(Money::mul('1200000', '3.000000'), '1000000'),
            Money::div(Money::mul('800000', '12.000000'), '1000000'),
            Money::div(Money::mul('2500000', '0.300000'), '1000000'),
            Money::div(Money::mul('83333', '3.750000'), '1000000'),
        ));
    }

    public function test_round_half_up(): void
    {
        self::assertSame('14.262499', Money::roundHalfUp('14.26249875', 6));
        self::assertSame('2.000000', Money::roundHalfUp('1.9999995', 6));
        self::assertSame('2.000001', Money::roundHalfUp('2.0000005', 6));
        self::assertSame('3', Money::roundHalfUp('2.5', 0));
        self::assertSame('0.000000', Money::roundHalfUp('0', 6));
    }

    public function test_format_normalizes_to_six_places(): void
    {
        self::assertSame('8.400000', Money::format('8.4', 6));
        self::assertSame('-5.000000', Money::format('-5', 6));
        self::assertSame('0.000000', Money::format('0', 6));
    }

    public function test_sub(): void
    {
        self::assertSame('1.5000000000000000', Money::sub('4.250000', '2.750000'));
    }
}
