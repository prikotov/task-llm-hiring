<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testRoundHalfUpToSix(): void
    {
        // Пример из задания: 14.26249875 → 14.262499
        self::assertSame('14.262499', Money::round6('14.26249875'));
        // HALF_UP: 6-й знак ровно на половине
        self::assertSame('2.500001', Money::round6('2.5000005'));
        self::assertSame('2.500000', Money::round6('2.5000004'));
        self::assertSame('3.000000', Money::round6('2.9999995'));
        // Нулевые и маленькие значения
        self::assertSame('0.000000', Money::round6('0'));
        self::assertSame('0.000000', Money::round6('0.0000001'));
        self::assertSame('0.000001', Money::round6('0.0000005'));
    }

    public function testRoundHandlesNegativeAmounts(): void
    {
        // HALF_UP по модулю, знак сохраняется
        self::assertSame('-2.500001', Money::round6('-2.5000005'));
        self::assertSame('-2.500000', Money::round6('-2.5000004'));
        // Отрицательное, которое после округления обнуляется
        self::assertSame('0.000000', Money::round6('-0.0000001'));
    }

    public function testFormatPadsToSixDecimals(): void
    {
        self::assertSame('8.400000', Money::format6('8.4'));
        self::assertSame('0.000000', Money::format6('0'));
        self::assertSame('-1.250000', Money::format6('-1.25'));
    }

    public function testArithmeticIsExact(): void
    {
        // Классическая ошибка float-арифметики не воспроизводится
        self::assertSame('14.2624987500000000', bcdiv('14262498.75', '1000000', 16));

        $sum = Money::add(Money::mul('2000000', '1.500000'), Money::mul('1000000', '8.000000'));
        self::assertSame('11.0000000000000000', bcdiv($sum, '1000000', 16));
    }
}
