<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public static function roundHalfUpProvider(): iterable
    {
        // [значение, scale, ожидалось]
        yield 'событие из примера задания' => ['14.26249875', 6, '14.262499'];
        yield 'ровно половина — вверх' => ['14.2624985', 6, '14.262499'];
        yield 'чуть меньше половины — вниз' => ['14.26249849999', 6, '14.262498'];
        yield '2.5 к нулю → 3' => ['2.5', 0, '3'];
        yield '2.499 к нулю → 2' => ['2.499', 0, '2'];
        yield 'целое без дробной' => ['11', 6, '11.000000'];
        yield 'ноль' => ['0', 6, '0.000000'];
        yield 'множество знаков — без потери точности' => ['0.12345678901234567', 10, '0.1234567890'];
        yield 'отрицательная половина — от нуля' => ['-2.5', 0, '-3'];
    }

    #[DataProvider('roundHalfUpProvider')]
    public function testRoundHalfUp(string $value, int $scale, string $expected): void
    {
        self::assertSame(
            $expected,
            Money::fromDecimal($value)->roundHalfUp($scale)->format($scale),
        );
    }

    public function testPrecisionIsNotLostBeforeRounding(): void
    {
        // 83 333 × 3.75 / 1 000 000 — дробное, не укладывается в 6 знаков
        $value = Money::fromDecimal('83333')
            ->multiplyBy('3.75')
            ->perMillion();

        self::assertSame('0.31249875', $value->format(8));
    }

    public function testAdditionSumsFullPrecision(): void
    {
        // 3.6 + 9.6 + 0.75 + 0.31249875 = 14.26249875
        $sum = Money::fromDecimal('3.6')
            ->add(Money::fromDecimal('9.6'))
            ->add(Money::fromDecimal('0.75'))
            ->add(Money::fromDecimal('0.31249875'));

        self::assertSame('14.26249875', $sum->format(8));
    }

    public function testFormatPadsToScale(): void
    {
        self::assertSame('11.000000', Money::fromDecimal('11')->format(6));
        self::assertSame('8.400000', Money::fromDecimal('8.4')->format(6));
    }

    public function testZeroSumOfRoundedValuesKeepsSixDecimals(): void
    {
        // сумма округлённых стоимостей событий, без повторного округления
        $sum = Money::fromDecimal('14.262499')
            ->add(Money::fromDecimal('11.000000'))
            ->add(Money::fromDecimal('8.400000'));

        self::assertSame('33.662499', $sum->format(6));
    }

    public function testInvalidDecimalIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::fromDecimal('1e3');
    }

    public function testNegativeScaleIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::fromDecimal('1')->roundHalfUp(-1);
    }
}
