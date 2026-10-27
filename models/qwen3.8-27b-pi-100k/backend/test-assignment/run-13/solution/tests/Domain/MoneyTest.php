<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[Test]
    public function testMul(): void
    {
        self::assertSame('1.2', Money::format(Money::mul('1.2', '1')));
        self::assertSame('6', Money::format(Money::mul('2', '3')));
    }

    #[Test]
    public function testDiv(): void
    {
        self::assertSame('0.5', Money::format(Money::div('1', '2')));
        self::assertSame('2.5', Money::format(Money::div('5', '2')));
    }

    #[Test]
    public function testAdd(): void
    {
        self::assertSame('1.3', Money::format(Money::add('1', '0.3')));
        self::assertSame('5.9', Money::format(Money::add('2.95', '2.95')));
    }

    #[Test]
    public function testRound(): void
    {
        self::assertSame('1.5', Money::round('1.5', 1));
        self::assertSame('14.262499', Money::round('14.26249875', 6));
    }

    #[Test]
    public function testFormat(): void
    {
        self::assertSame('1.25', Money::format('1.25'));
        self::assertSame('1.2', Money::format('1.20000'));
        self::assertSame('0', Money::format('0.0000'));
        self::assertSame('5', Money::format('5.000000'));
    }

    #[Test]
    public function testRateCost(): void
    {
        // 1200000 tokens at 3.00 per 1M = 3.6
        $cost = Money::div(Money::mul('1200000', '3'), '1000000');
        self::assertSame('3.6', Money::format($cost));
    }

    #[Test]
    public function testCostCalculation(): void
    {
        // e1: input=1200000@3, output=800000@12, cache_read=2500000@0.3, cache_write=83333@3.75
        // = (3600000 + 9600000 + 750000 + 312498.75) / 1e6 = 14.26249875 → 14.262499
        $a = Money::div(Money::mul('1200000', '3'), '1000000');
        $b = Money::div(Money::mul('800000', '12'), '1000000');
        $c = Money::div(Money::mul('2500000', '0.3'), '1000000');
        $d = Money::div(Money::mul('83333', '3.75'), '1000000');

        $sum = Money::add(Money::add(Money::add($a, $b), $c), $d);
        self::assertSame('14.262499', Money::round($sum, 6));
    }

    #[Test]
    public function testOffPeakMultiplier(): void
    {
        // cost 14.26249875 × 0.5 = 7.131249375 → 7.131249
        $cost = Money::mul('14.26249875', '0.5');
        self::assertSame('7.131249', Money::round($cost, 6));
    }
}
