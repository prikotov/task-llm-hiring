<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Shared\Decimal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DecimalTest extends TestCase
{
    #[DataProvider("provideNormalize")]
    public function testNormalize(string $input, string $expected): void
    {
        self::assertSame($expected, Decimal::normalize($input));
    }

    public static function provideNormalize(): iterable
    {
        yield 'zero' => ['0', '0'];
        yield 'trailing zeros' => ['8.400000', '8.4'];
        yield 'leading fractional zeros' => ['0.005', '0.005'];
        yield 'leading integer zeros' => ['00.500', '0.5'];
        yield 'negative' => ['-1.2300', '-1.23'];
        yield 'negative zero' => ['-0.000000', '0'];
        yield 'plus sign' => ['+3', '3'];
        yield 'plain int' => ['123456789', '123456789'];
        yield 'large with scale' => ['14.2624987500000000000000000000000', '14.26249875'];
    }

    #[DataProvider("provideRoundHalfUp")]
    public function testRoundHalfUp(string $value, int $digits, string $expected): void
    {
        self::assertSame($expected, Decimal::roundHalfUp($value, $digits));
    }

    public static function provideRoundHalfUp(): iterable
    {
        // Пример из TASK.md: 2.5 -> 3
        yield 'example 2.5->3' => ['2.5', 0, '3'];
        yield 'half up six' => ['14.2624985', 6, '14.262499'];
        yield 'fixture e1' => ['14.26249875', 6, '14.262499'];
        yield 'already six' => ['8.4', 6, '8.4'];
        yield 'negative' => ['-2.5', 0, '-3'];
        yield 'negative six' => ['-14.2624985', 6, '-14.262499'];
        yield 'zero' => ['0', 6, '0'];
        yield 'no-op when short' => ['3.000000', 6, '3'];
        yield 'truncate beyond' => ['0.123456789', 6, '0.123457'];
    }

    #[DataProvider("provideFormat6")]
    public function testFormat6(string $value, string $expected): void
    {
        self::assertSame($expected, Decimal::format6($value));
    }

    public static function provideFormat6(): iterable
    {
        yield 'fixture e1' => ['14.262499', '14.262499'];
        yield 'fixture e3' => ['8.4', '8.400000'];
        yield 'zero' => ['0', '0.000000'];
        yield 'negative whole' => ['-2', '-2.000000'];
        yield 'negative fractional' => ['-0.6', '-0.600000'];
        yield 'negative zero keeps canonical' => ['-0.000000', '0.000000'];
        yield 'integer' => ['123456', '123456.000000'];
        yield 'short fraction' => ['11', '11.000000'];
    }

    public function testArithmeticIsExact(): void
    {
        // 83333 × 3.75 / 1 000 000 — «плывущая по float» дробь.
        self::assertSame(
            '0.31249875',
            Decimal::normalize(Decimal::div(Decimal::mul('83333', '3.75'), '1000000')),
        );
        // HALF_UP до 6 знаков: 0.31249875 -> 0.312499.
        self::assertSame('0.312499', Decimal::roundHalfUp(Decimal::div(Decimal::mul('83333', '3.75'), '1000000'), 6));

        // Сумма отчёта по фикстурам:
        $sum = Decimal::sum(['14.262499', '8.4', '11']);
        self::assertSame('33.662499', $sum);
        self::assertSame('33.662499', Decimal::format6($sum));

        self::assertSame('-0.6', Decimal::sub('22.662499', '23.262499'));
    }

    public function testDivByZeroThrows(): void
    {
        $this->expectException(RuntimeException::class);
        Decimal::div('1', '0');
    }
}
