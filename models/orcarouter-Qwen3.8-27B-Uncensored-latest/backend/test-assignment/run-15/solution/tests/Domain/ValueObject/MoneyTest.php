<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_additionsAreExact(): void
    {
        $sum = Money::from('0.1')->add(Money::from('0.2'));
        self::assertNotSame(
            '0.30000000000000004441',
            $sum->toString(),
            '0.1 + 0.2 must not show float drift'
        );
        self::assertSame('0.3', trimZeros($sum->toString()));
    }

    public function test_fromStringKeepsExactValue(): void
    {
        $m = Money::from('123.456789');
        self::assertSame('123.456789', trimZeros($m->toString()));
        self::assertSame('123.456789', $m->roundTo(6)->format(6));
    }

    public function test_multiplicationIsExact(): void
    {
        $m = Money::from('3.335')->multiplyBy('10');
        self::assertSame('33.35', trimZeros($m->toString()));
    }

    public function test_roundToHalfUpAtExactHalf(): void
    {
        self::assertSame('3',        Money::from('2.5')->roundTo(0)->format(0));
        self::assertSame('2',        Money::from('2.4')->roundTo(0)->format(0));
        self::assertSame('2.500000', Money::from('2.4999995')->roundTo(6)->format(6));
        self::assertSame('2.500001', Money::from('2.5000005')->roundTo(6)->format(6));
    }

    public function test_roundToHalfUpCarryThroughInteger(): void
    {
        self::assertSame('1000000.000000', Money::from('999999.9999999')->roundTo(6)->format(6));
    }

    public function test_roundToHalfUpNegative(): void
    {
        self::assertSame('-3', Money::from('-2.5')->roundTo(0)->format(0));
        self::assertSame('-2', Money::from('-2.4')->roundTo(0)->format(0));
    }

    public function test_formatAlwaysShowsRequestedDecimals(): void
    {
        self::assertSame('5.000000', Money::from('5')->format(6));
        self::assertSame('0.000000', Money::from('0')->format(6));
    }

    public function test_smallValuesRoundCorrectly(): void
    {
        self::assertSame('0.123457', Money::from('0.1234567')->roundTo(6)->format(6));
        self::assertSame('3.141593', Money::from('3.14159265')->roundTo(6)->format(6));
    }
}

function trimZeros(string $n): string
{
    if (!str_contains($n, '.')) {
        return $n;
    }
    return rtrim(rtrim($n, '0'), '.');
}
