<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public static function roundingCases(): iterable
    {
        yield 'basic half-up' => ['14.26249875', '14.262499'];
        yield 'zero-remainder zero' => ['8.4', '8.400000'];
        yield 'no rounding (5 not there)' => ['2.5', '2.500000'];
        yield 'carry over integer' => ['0.9999995', '1.000000'];
        yield 'tiny round up' => ['0.0000005', '0.000001'];
        yield 'tiny round down' => ['0.0000004', '0.000000'];
        yield 'trailing zero' => ['11.0', '11.000000'];
        yield 'integer' => ['100', '100.000000'];
        yield '17 digits' => ['12345678.901234567', '12345678.901235'];
        yield 'neg round' => ['-3.1415929', '-3.141593'];
    }

    /**
     * @dataProvider roundingCases
     */
    public function testRoundHalfUpTo6(string $input, string $expected): void
    {
        $m = Money::of($input);
        $out = $m->roundHalfUpTo6()->toFixed6();
        self::assertSame($expected, $out, "$input should round to $expected, got $out");
    }

    public function testAdd(): void
    {
        self::assertSame('12.75', Money::of('10.5')->add(Money::of('2.25'))->value());
        self::assertSame('0', Money::zero()->add(Money::zero())->value());
        self::assertSame('-3.4', Money::of('-5.1')->add(Money::of('1.7'))->value());
    }

    public function testSubtract(): void
    {
        self::assertSame('7.75', Money::of('10.5')->subtract(Money::of('2.75'))->value());
        self::assertSame('0', Money::of('5')->subtract(Money::of('5'))->value());
    }

    public function testMultiply(): void
    {
        self::assertSame('50', Money::of('100')->multiply('0.5')->value());
        self::assertSame('3.6', Money::of('1.2')->multiply('3')->value());
        self::assertSame('1.575', Money::of('1.2')->multiply('1.3125')->value());
    }

    public function testNegateZero(): void
    {
        self::assertSame('0', Money::zero()->negate()->value());
    }

    public function testNegativeZeroNormalizedToZero(): void
    {
        self::assertSame('0', Money::of('-0')->value());
    }
}
