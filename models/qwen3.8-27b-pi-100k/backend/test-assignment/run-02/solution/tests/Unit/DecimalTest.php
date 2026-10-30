<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Shared\Math\Decimal;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ValueError;

final class DecimalTest extends TestCase
{
    public function testMoney6PadsShortValuesToSixDecimals(): void
    {
        self::assertSame('14.262499', Decimal::money6('14.262499'));
        self::assertSame('14.262500', Decimal::money6('14.2625'));
        self::assertSame('8.400000', Decimal::money6('8.4'));
        self::assertSame('0.000000', Decimal::money6('0'));
        self::assertSame('100.000000', Decimal::money6('100'));
    }

    public function testMoney6AppliesSingleHalfUpRoundingBeyondSixDecimals(): void
    {
        self::assertSame('14.262500', Decimal::money6('14.2624995')); // ровно 0.5 — вверх
        self::assertSame('14.262499', Decimal::money6('14.2624994')); // ниже 0.5 — вниз
        self::assertSame('7.131249', Decimal::money6('7.131249375'));
        self::assertSame('0.000002', Decimal::money6('0.0000015'));
        self::assertSame('0.000001', Decimal::money6('0.0000014'));
    }

    public function testMoney6RejectsNegativeValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Decimal::money6('-0.000001');
    }

    public function testMoney6RejectsEmptyValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Decimal::money6('');
    }

    public function testRoundHalfUp6(): void
    {
        self::assertSame('0.000001', Decimal::roundHalfUp6('0.0000005'));
        self::assertSame('1.500000', Decimal::roundHalfUp6('1.4999995'));
        self::assertSame('1.500000', Decimal::roundHalfUp6('1.4999996'));
    }

    public function testSumKeepsSixDecimalPrecision(): void
    {
        // Сумма итоговых стоимостей событий (каждая ≤ 6 знаков) — без округления.
        self::assertSame(
            '33.662499',
            Decimal::sum(['14.262499', '8.400000', '11.000000']),
        );
        self::assertSame(
            '14.262499',
            Decimal::sum(['14.262499', '0.000000']),
        );
    }

    public function testDivRejectsZeroDivisor(): void
    {
        $this->expectException(ValueError::class);
        Decimal::div('1', '0');
    }
}
