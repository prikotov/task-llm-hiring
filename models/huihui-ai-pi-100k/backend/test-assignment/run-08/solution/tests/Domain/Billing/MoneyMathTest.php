<?php

declare(strict_types=1);

namespace App\Tests\Domain\Billing;

use App\Domain\Billing\MoneyMath;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyMathTest extends TestCase
{
    #[Test]
    public function roundHalfUpTo6RoundsUpAtHalfMicro(): void
    {
        self::assertSame('14.262499', MoneyMath::roundHalfUpTo6('14.26249875'));
        self::assertSame('11.000000', MoneyMath::roundHalfUpTo6('11.0'));
        self::assertSame('8.400000', MoneyMath::roundHalfUpTo6('8.40000000'));
        self::assertSame('2.999999', MoneyMath::roundHalfUpTo6('2.9999994'));
        self::assertSame('3.000000', MoneyMath::roundHalfUpTo6('2.9999995'));
    }

    #[Test]
    public function roundHalfUpTo6IsExactForTaskExample(): void
    {
        // 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M = 14.26249875
        $expr = MoneyMath::add(
            MoneyMath::add(
                MoneyMath::add(
                    MoneyMath::div(MoneyMath::mul('1200000', '3.000000'), '1000000'),
                    MoneyMath::div(MoneyMath::mul('800000', '12.000000'), '1000000'),
                ),
                MoneyMath::div(MoneyMath::mul('2500000', '0.300000'), '1000000'),
            ),
            MoneyMath::div(MoneyMath::mul('83333', '3.750000'), '1000000'),
        );

        self::assertSame('14.262499', MoneyMath::roundHalfUpTo6($expr));
    }

    #[Test]
    public function formatTo6AlwaysEmitsSixDecimals(): void
    {
        self::assertSame('0.000000', MoneyMath::formatTo6(MoneyMath::zero()));
        self::assertSame('3.000000', MoneyMath::formatTo6('3'));
        self::assertSame('17.862499', MoneyMath::formatTo6('17.862499'));
        self::assertSame('33.662499', MoneyMath::formatTo6(MoneyMath::add('14.262499', MoneyMath::add('11.000000', '8.400000'))));
    }

    #[Test]
    public function arithmeticStaysDecimal(): void
    {
        self::assertSame('0.300000', bcdiv(MoneyMath::mul('1', '0.300000'), '1', 6));
        self::assertSame('0.5000000000000000', MoneyMath::mul('1.000000', '0.5'));
    }
}
