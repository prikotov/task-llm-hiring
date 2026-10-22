<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function roundHalfUpProvider(): array
    {
        return [
            'пример из ТЗ: e1' => ['14.26249875', '14.262499'],
            'точка ровно на середине (2.5 -> 3)' => ['2.5', '2.500000'],
            'шестой знак: ровно 5 — вверх' => ['0.0000005', '0.000001'],
            'шестой знак: окр. до 10-й' => ['1.0000005', '1.000001'],
            'чуть ниже середины — вниз' => ['1.0000004999', '1.000000'],
            'нулевая стоимость' => ['0', '0.000000'],
            'целая сумма' => ['8.4', '8.400000'],
            'крупная сумма без потери точности' => ['123456789.12345678', '123456789.123457'],
            '99999999.9999995' => ['99999999.9999995', '100000000.000000'],
        ];
    }

    #[DataProvider('roundHalfUpProvider')]
    public function testRoundHalfUpTo6(string $input, string $expected): void
    {
        self::assertSame($expected, Money::of($input)->roundTo6()->format6());
    }

    public function testExactAdditionWithoutRounding(): void
    {
        $sum = Money::zero();
        foreach (['14.262499', '8.400000', '11.000000'] as $part) {
            $sum = $sum->add(Money::of($part));
        }

        self::assertSame('33.662499', $sum->format6());
    }

    public function testMultiplicationByOffPeakMultiplier(): void
    {
        $value = Money::of('3.000000')->multipliedBy('0.5');

        self::assertSame('1.500000', $value->roundTo6()->format6());
    }

    public function testDecimalStringValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::of('1e3');
    }
}
