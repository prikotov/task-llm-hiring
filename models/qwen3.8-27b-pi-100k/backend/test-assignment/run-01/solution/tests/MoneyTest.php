<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Money;
use PHPUnit\Framework\TestCase;
use ValueError;

final class MoneyTest extends TestCase
{
    private function decimalEq(string $a, string $b): void
    {
        self::assertSame(0, Money::cmp($a, $b), sprintf('Должно быть: %s, получено: %s', $b, $a));
    }

    public function testRoundingIsHalfUpNotHalfEven(): void
    {
        self::assertSame('1', Money::roundHalfUp('0.5', 0));
        self::assertSame('3', Money::roundHalfUp('2.5', 0));
        // При HALF_EVEN вышло бы 0.500002 (среди 6 знаков «2» чётное) — HALF_UP даёт 0.500003.
        self::assertSame('0.500003', Money::roundHalfUp('0.5000025'));
        self::assertSame('14.262499', Money::roundHalfUp('14.26249875'));
        self::assertSame('14.2625', Money::roundHalfUp('14.26249875', 4));
    }

    public function testArithmeticIsExact(): void
    {
        // Классическая ловушка float: 1.2 + 2.404.
        self::decimalEq('3.604', Money::add('1.2', '2.404'));
        self::decimalEq('1.204', Money::sub('3.604', '2.4'));
        self::decimalEq('7.2', Money::mul('2.4', '3'));
        self::decimalEq('0.000003', Money::div('3', '1000000'));
        self::assertSame(0, Money::cmp('0.100', '0.1'), 'Заведённые ноли после точки не меняют величину');
        self::assertSame(-1, Money::cmp('1.999999', '2'));
    }

    public function testDivisionByZeroIsRejected(): void
    {
        $this->expectException(ValueError::class);
        Money::div('1', '0');
    }

    public function testNegativeValueCannotBeRounded(): void
    {
        $this->expectException(ValueError::class);
        Money::roundHalfUp('-0.1');
    }

    public function testFormatPadsToExactScale(): void
    {
        self::assertSame('8.400000', Money::format('8.4'));
        self::assertSame('0.000000', Money::format('0'));
        self::assertSame('1200000', Money::format('1200000', 0));
    }
}
