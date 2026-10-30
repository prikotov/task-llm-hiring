<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Billing\Decimal;
use PHPUnit\Framework\TestCase;

/**
 * Тесты точной десятичной арифметики и HALF_UP до 6 знаков.
 */
final class DecimalTest extends TestCase
{
    public function testZero(): void
    {
        self::assertSame('0.000000', Decimal::fromInt(0)->roundHalfUp6()->toFixed(6));
    }

    public function testPositiveMultiplication(): void
    {
        self::assertSame(
            '3600000000000/10^6',
            Decimal::fromInt(1200000)->multiply(Decimal::fromString('3.000000'))->parts()['p'] . '/10^' .
            Decimal::fromInt(1200000)->multiply(Decimal::fromString('3.000000'))->parts()['k']
        );
    }

    public function testExactDivision(): void
    {
        self::assertSame(
            '14.26249875',
            (string) Decimal::exactDivide('14262498750000', 6, 1_000_000)
        );
    }

    public function testRoundHalfUpPositive(): void
    {
        self::assertSame(
            '14.262499',
            Decimal::exactDivide('14262498750000', 6, 1_000_000)->roundHalfUp6()->toFixed(6)
        );
    }

    public function testRoundHalfUpNegative(): void
    {
        self::assertSame(
            '-1.000001',
            Decimal::fromString('-1.0000005')->roundHalfUp6()->toFixed(6)
        );
    }

    public function testNegativeRoundHalfUpTowardAway(): void
    {
        // -0.0000005 → -0.000001 (HALF_UP от нуля — в сторону бесконечности)
        self::assertSame(
            '-0.000001',
            Decimal::fromString('-0.0000005')->roundHalfUp6()->toFixed(6)
        );
        self::assertSame(
            '0.000001',
            Decimal::fromString('+0.0000005')->roundHalfUp6()->toFixed(6)
        );
    }

    public function testAddSubtract(): void
    {
        self::assertSame(
            '8.400000',
            Decimal::fromString('8.0')->add(Decimal::fromString('0.4'))->toFixed(6)
        );
        self::assertSame(
            '4.250000',
            Decimal::fromString('1.25')->add(Decimal::fromString('3'))->toFixed(6)
        );
        self::assertSame(
            '-4.000000',
            Decimal::fromString('-1.5')->add(Decimal::fromString('-2.5'))->toFixed(6)
        );
        self::assertSame(
            '1.000000',
            Decimal::fromString('-1.5')->add(Decimal::fromString('2.5'))->toFixed(6)
        );
        self::assertSame(
            '0.300000',
            Decimal::fromString('0.1')->add(Decimal::fromString('0.2'))->toFixed(6)
        );
    }

    public function testMultiplicationNegative(): void
    {
        self::assertSame(
            '-6.000000',
            Decimal::fromInt(2)->multiply(Decimal::fromString('-3.000000'))->toFixed(6)
        );
    }
}
