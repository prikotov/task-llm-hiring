<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Money;
use PHPUnit\Framework\TestCase;

/**
 * Тестирует десятичную арифметику bcmath: нет float, нет накопления
 * ошибки, корректность HALF_UP-округления до 6 знаков.
 */
final class MoneyTest extends TestCase
{
    private const CASES = [
        // [a, b, expected a+b]
        ['3.600000', '9.600000', '13.200000'],
        ['0.1', '0.2', '0.3'],
        ['14.2624987', '0.00000005', '14.26249875'],
        ['1000000.5', '999999.499999', '1999999.999999'],
        ['1', '2.34567891', '3.34567891'],
        ['0', '0', '0'],
        ['-5.5', '2.5', '-3.0'],
        ['-1.000001', '1.000001', '0'],
        ['10000000000000', '0.0625', '10000000000000.0625'],
        ['666666.666666', '333333.333334', '1000000.000000'],
        ['1.005', '1.005', '2.010'],
    ];

    /**
     * @dataProvider provideAdd
     */
    public function testAdd(string $a, string $b, string $expected): void
    {
        self::assertSame(0, bccomp(Money::add($a, $b), $expected, 12));
    }

    public static function provideAdd(): iterable
    {
        foreach (self::CASES as [$a, $b, $expected]) {
            yield "$a + $b" => [$a, $b, $expected];
        }
    }

    public function testSub(): void
    {
        self::assertSame(0, bccomp(Money::sub('14.262499', '3.600000'), '10.662499', 12));
        self::assertSame(0, bccomp(Money::sub('11.000000', '20.000000'), '-9.000000', 12));
        self::assertSame(0, bccomp(Money::sub('0.000001', '0.000001'), '0', 12));
    }

    public function testMul(): void
    {
        self::assertSame(0, bccomp(Money::mul('1.200000', '3.000000'), '3.600000', 12));
        self::assertSame(0, bccomp(Money::mul('0.5', '14.262500625'), '7.1312503125', 16));
        self::assertSame(0, bccomp(Money::mul('0.25', '600.000000'), '150.000000', 12));
        self::assertSame(0, bccomp(Money::mul('0', '100'), '0', 12));
        self::assertSame(0, bccomp(Money::mul('-2', '3.5'), '-7.0', 12));
    }

    public function testDiv(): void
    {
        self::assertSame(0, bccomp(Money::div('1000000', '4'), '250000', 12));
        self::assertSame(0, bccomp(Money::div('1', '3'), '0.3333333333333333', 16));
    }

    public function testHalfUp6(): void
    {
        self::assertSame('0.000000', Money::halfUp6('0'));
        self::assertSame('13.200000', Money::halfUp6('13.2'));
        self::assertSame('0.000001', Money::halfUp6('0.000001'));
        self::assertSame('14.262499', Money::halfUp6('14.26249875'));
        self::assertSame('20.000000', Money::halfUp6('20'));
        self::assertSame('3.345679', Money::halfUp6('3.34567891'));
        self::assertSame('0.123455', Money::halfUp6('0.1234545'));  // ровно посередине: дальше от нуля -> вверх
        self::assertSame('0.123454', Money::halfUp6('0.1234544999'));
        self::assertSame('999999.999999', Money::halfUp6('999999.9999989999'));
        self::assertSame('1000000.000000', Money::halfUp6('999999.9999999'));
        // HALF_UP: ровно 0.0000005 -> вверх до 0.000001
        self::assertSame('0.000001', Money::halfUp6('0.0000005'));
        self::assertSame('0.000000', Money::halfUp6('0.0000004'));
    }

    public function testTo6FormatsSixDecimals(): void
    {
        self::assertSame('0.000000', Money::to6('0'));
        self::assertSame('11.000000', Money::to6('11'));
        self::assertSame('-9.000000', Money::to6('-9'));
        self::assertSame('42.662499', Money::to6('42.662499'));
        self::assertSame('14.262499', Money::to6('14.26249875'));
    }
}
