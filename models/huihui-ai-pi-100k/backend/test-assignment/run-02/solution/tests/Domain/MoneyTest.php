<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Точная десятичная арифметика: float не участвует в вычислениях денег.
 */
final class MoneyTest extends TestCase
{
    public function testZeroAndFromDecimalString(): void
    {
        self::assertSame('0', Money::zero()->toDecimalString());
        self::assertSame('14.262499', Money::fromDecimalString('14.262499')->toDecimalString());
        self::assertSame('-0.75', Money::fromDecimalString('-0.75')->toDecimalString());
    }

    public function testPlusMinusIsExact(): void
    {
        // 0.1 + 0.2 — классическая ловушка float
        self::assertSame(
            '0.3',
            Money::fromDecimalString('0.1')->plus(Money::fromDecimalString('0.2'))->toDecimalString(),
        );

        // сумма из разделов задания
        $sum = Money::zero();
        foreach (['14.262499', '11.000000', '8.400000'] as $part) {
            $sum = $sum->plus(Money::fromDecimalString($part));
        }

        self::assertSame('33.662499', $sum->toDecimalString());

        // delta = old - new
        $delta = Money::fromDecimalString('14.262499')
            ->minus(Money::fromDecimalString('14.982499'));
        self::assertSame('-0.72', $delta->toDecimalString());
    }

    public function testTimesAndDividedByKeepPrecision(): void
    {
        // cost = 83333 * 3.75 / 1_000_000 — как в разделении «Расчёт стоимости»
        $cost = Money::fromDecimalString('83333')
            ->times('3.75')
            ->dividedBy('1000000');

        self::assertSame('0.31249875', $cost->toDecimalString());
        self::assertSame('0.312499', $cost->format());
    }

    public function testRoundHalfUpToSixDecimals(): void
    {
        // ровно полушаг — всегда вверх
        self::assertSame('0.000001', Money::fromDecimalString('0.0000005')->roundHalfUp(6)->toDecimalString());
        self::assertSame('0.000002', Money::fromDecimalString('0.0000015')->roundHalfUp(6)->toDecimalString());
        self::assertSame('0.000002', Money::fromDecimalString('0.0000015')->format());
        // 14.2624985 → 14.262499 (зачётный пример: 0.0000005 округляется вверх)
        self::assertSame('14.262499', Money::fromDecimalString('14.2624985')->format());
        // менее полушага — вниз
        self::assertSame('0.000001', Money::fromDecimalString('0.0000014')->format());
    }

    public function testFormatAlwaysSixDigitals(): void
    {
        self::assertSame('14.262499', Money::fromDecimalString('14.262499375')->format());
        self::assertSame('11.000000', Money::fromDecimalString('11')->format());
        self::assertSame('8.400000', Money::fromDecimalString('8.4')->format());
        self::assertSame('0.000000', Money::zero()->format());
        self::assertSame('-0.750000', Money::fromDecimalString('-0.75')->format());
    }

    #[DataProvider('invalidDecimals')]
    public function testInvalidDecimalStringsAreRejected(string $value): void
    {
        $this->expectException(\ValueError::class);

        Money::fromDecimalString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidDecimals(): iterable
    {
        yield 'нет числа' => ['abc'];
        yield 'две точки' => ['1.2.3'];
        yield 'пробел' => [' 12.5'];
        yield 'научная запись' => ['1e3'];
        yield 'пустая строка' => [''];
        yield 'только точка' => ['.5'];
    }

    public function testNullIsRejectedByType(): void
    {
        $this->expectException(\TypeError::class);

        Money::fromDecimalString(null);
    }

    public function testFloatIsRejectedByType(): void
    {
        $this->expectException(\TypeError::class);

        Money::fromDecimalString(12.5); // float для денег запрещён даже на уровне типа
    }

    public function testBooleanIsRejectedByType(): void
    {
        $this->expectException(\TypeError::class);

        Money::fromDecimalString(true);
    }
}
