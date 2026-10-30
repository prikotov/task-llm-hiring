<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\ValueObject\Decimal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    public function testArithmeticIsExact(): void
    {
        self::assertSame('3.6', Decimal::of('1.2')->mul(Decimal::of('3.000000'))->value());
        self::assertSame(
            '14.26249875',
            Decimal::of('3.6')
                ->add(Decimal::of('9.6'))
                ->add(Decimal::of('0.75'))
                ->add(Decimal::of('0.31249875'))
                ->value(),
        );
    }

    public function testDivisionKeepsPrecision(): void
    {
        // 83333 × 3.75 / 1 000 000 = 0.31249875 — ровно, без float-помех.
        self::assertSame(
            '0.31249875',
            Decimal::of(83333)->mul(Decimal::of('3.75'))->div(Decimal::of('1000000'))->value(),
        );
    }

    public function testNegativeAndZeroNormalization(): void
    {
        self::assertSame('0', Decimal::of('-0')->value());
        self::assertSame('0', Decimal::of('0.000')->value());
        self::assertSame('0.5', Decimal::of('0.500000')->value());
    }

    public function testInvalidInputRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Decimal('1.2.3');
    }

    #[DataProvider('roundingProvider')]
    public function testRoundHalfUp(string $value, int $digits, string $expected): void
    {
        self::assertSame($expected, Decimal::of($value)->round($digits)->toFixed($digits));
    }

    /** @return iterable<string, array{string, int, string}> */
    public static function roundingProvider(): iterable
    {
        yield 'пример из задачи' => ['14.26249875', 6, '14.262499'];
        yield 'половина в большую (2.5 → 3)' => ['2.5', 0, '3'];
        yield 'половина при 0.005 → 0.01' => ['0.005', 2, '0.01'];
        yield 'ровные шестерки сохраняются' => ['11.0', 6, '11.000000'];
        yield '8.4 → 8.400000' => ['8.4', 6, '8.400000'];
    }

    public function testRoundDoesNotTruncateWithoutRounding(): void
    {
        // HALF_UP: 9.995 → 10.00 (не усечение до 9.99).
        self::assertSame('10.00', Decimal::of('9.995')->round(2)->toFixed(2));
    }

    public function testToFixedPadsZeros(): void
    {
        self::assertSame('11.000000', Decimal::of('11')->toFixed(6));
        self::assertSame('0.500000', Decimal::of('0.5')->toFixed(6));
    }
}
