<?php

declare(strict_types=1);

namespace App\Tests\Domain\Money;

use App\Domain\Money\MoneyMath;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * HALF_UP-округление и точность bcmath-операций (TASK.md, правило 4).
 */
final class MoneyMathTest extends TestCase
{
    #[Test]
    public function roundHalfUpToSixPlaces(): void
    {
        self::assertSame('14.262499', MoneyMath::round('14.26249875'));
        self::assertSame('0.000001', MoneyMath::round('0.0000005'));
        self::assertSame('0.000000', MoneyMath::round('0.0000004'));
        self::assertSame('1.000000', MoneyMath::round('1'));
        self::assertSame('3', MoneyMath::round('2.5', 0));
        self::assertSame('-14.262499', MoneyMath::round('-14.26249875'));
    }

    #[Test]
    public function addPreservesLongFractionalTail(): void
    {
        // 83333 × 3.75 = 312 498.75 → /1M = 0.31249875 — ровно 6 знаков
        // хвоста, которые float потерял бы.
        $value = MoneyMath::div(MoneyMath::mul('83333', '3.750000'), '1000000');

        self::assertSame('0.312498750000000000000000', $value);
    }

    #[Test]
    public function normalize6PadsToSixPlacesWithoutChangingValue(): void
    {
        self::assertSame('0.000000', MoneyMath::normalize6('0'));
        self::assertSame('14.262499', MoneyMath::normalize6('14.262499'));
        self::assertSame('8.400000', MoneyMath::normalize6('8.4'));
    }
}
