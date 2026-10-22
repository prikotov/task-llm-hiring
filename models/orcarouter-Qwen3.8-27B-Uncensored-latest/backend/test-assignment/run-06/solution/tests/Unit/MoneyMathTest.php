<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\MoneyMath;
use PHPUnit\Framework\TestCase;

final class MoneyMathTest extends TestCase
{
    public function testRoundsHalfUpToMicros(): void
    {
        // Из примера задания: 14.26249875 -> 14.262499 -> 14262499 микрос
        self::assertSame(14262499, MoneyMath::roundToMicros('14.26249875'));
        // HALF_UP: .5 округляется вверх (2.5 -> 3)
        self::assertSame(2500000, MoneyMath::roundToMicros('2.5'));
        self::assertSame(3500000, MoneyMath::roundToMicros('3.5'));
        // .499999 -> вниз
        self::assertSame(3499999, MoneyMath::roundToMicros('3.4999994'));
        self::assertSame(0, MoneyMath::roundToMicros('0'));
        self::assertSame(0, MoneyMath::roundToMicros('0.0000001'));
    }

    public function testFormatsMicrosWithExactlySixDecimals(): void
    {
        self::assertSame('14.262499', MoneyMath::formatMicros(14262499));
        self::assertSame('8.400000', MoneyMath::formatMicros(8400000));
        self::assertSame('0.200000', MoneyMath::formatMicros(200000));
        self::assertSame('0.000001', MoneyMath::formatMicros(1));
        self::assertSame('-1.400000', MoneyMath::formatMicros(-1400000));
        self::assertSame('0.000000', MoneyMath::formatMicros(0));
    }
}
