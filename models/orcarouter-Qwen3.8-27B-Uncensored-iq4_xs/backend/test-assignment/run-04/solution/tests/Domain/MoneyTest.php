<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public static function provideRounding(): array
    {
        return [
            'no fraction needed' => ['1.5', '1.500000'],
            'truncation of long exact value keeps 6 digits' => ['14.26249875', '14.262499'],
            'half up 2.5 -> 3 (spec example)' => ['2.5000005', '2.500001'],
            'half boundary at 6th digit round up' => ['0.0000005', '0.000001'],
            'negative half goes toward -inf boundary floor(x+1/2)' => ['-14.26249875', '-14.262499'],
            'zero' => ['0', '0.000000'],
            'large value' => ['123456789.123456789', '123456789.123457'],
        ];
    }

    #[DataProvider('provideRounding')]
    public function testRoundedHalfUpToFixed(string $value, string $expected): void
    {
        self::assertSame($expected, Money::exact($value)->roundedHalfUpToFixed()->asFixed6String());
    }

    public function testExactArithmeticWithoutFloat(): void
    {
        // 1 200 000 × 3 / 1 000 000
        $a = Money::exact('1200000')->multipliedBy('3')->dividedBy(1_000_000);
        self::assertSame('3.600000', $a->roundedHalfUpToFixed()->asFixed6String());

        // e1 из TASK.md: 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75, /1M
        $sum = Money::zero()
            ->add(Money::exact('1200000')->multipliedBy('3'))
            ->add(Money::exact('800000')->multipliedBy('12'))
            ->add(Money::exact('2500000')->multipliedBy('0.3'))
            ->add(Money::exact('83333')->multipliedBy('3.75'));
        self::assertSame('14.262499', $sum->dividedBy(1_000_000)->roundedHalfUpToFixed()->asFixed6String());
    }

    public function testAddSubtractAndEquals(): void
    {
        $sum = Money::exact('14.262499')->add(Money::exact('8.400000'));
        self::assertSame('22.662499', $sum->asFixed6String());
        self::assertSame('-5.862499', $sum->subtract(Money::exact('28.524998'))->asFixed6String());
        self::assertTrue(Money::exact('1.00')->equals(Money::exact('1.000000')));
        self::assertFalse(Money::exact('1.00')->equals(Money::exact('1.000001')));
    }

    public function testMultiplierPrecisionKeepsTail(): void
    {
        // 14.26249875 × 0.5 = 7.131249375 — хвост не теряет до финального округления
        $value = Money::exact('14.26249875')->multipliedBy('0.5');
        self::assertSame('7.131249', $value->roundedHalfUpToFixed()->asFixed6String());
    }

    public function testFromFixed6Roundtrip(): void
    {
        self::assertSame('14.262499', Money::fromFixed6('14.262499')->asFixed6String());
    }

    public function testInvalidValuesRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Money::exact('1e5');
    }

    public function testDivideByZeroRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Money::exact('1')->dividedBy(0);
    }

    public function testNegativeValueAccessors(): void
    {
        $neg = Money::exact('-2.5');
        self::assertTrue($neg->isNegative());
        self::assertSame('2.50000000000000000000', $neg->absoluteValue()->value());
    }
}
