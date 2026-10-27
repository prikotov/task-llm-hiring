<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money\Decimal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DecimalTest extends TestCase
{
    #[DataProvider('roundUpProvider')]
    public function testRoundHalfUp(string $value, int $places, string $expected): void
    {
        self::assertSame($expected, Decimal::roundHalfUp($value, $places));
    }

    public static function roundUpProvider(): array
    {
        return [
            'half rounds up' => ['0.1234565', 6, '0.123457'],
            'below half rounds down' => ['0.1234564', 6, '0.123456'],
            'zero tail unchanged' => ['1.06251234', 6, '1.062512'],
            'zero value' => ['0', 6, '0.000000'],
            'negative half up' => ['-0.0000005', 6, '-0.000001'],
            'no fractional overflow' => ['1.9999995', 6, '2.000000'],
        ];
    }

    public function testArithmeticIsExact(): void
    {
        self::assertSame('3.60000000000000000000', Decimal::perMillion('3600000'));
        self::assertSame('300.00000000000000000000', Decimal::mul('1', '300'));
        self::assertSame('0.30000000000000000000', Decimal::add('0.1', '0.2'));

        // Деление с запасом знаков — без потери точности в пределах SCALE.
        self::assertSameDecimal('1/3', Decimal::div('1', '3'));
        self::assertSame('0.00000000000000000000', Decimal::sub('0.5', '0.5'));
    }

    public function testToSixDecimalsKeepsValue(): void
    {
        self::assertSame('14.262499', Decimal::toSixDecimals('14.262499'));
        self::assertSame('8.400000', Decimal::toSixDecimals('8.4'));
        self::assertSame('0.000000', Decimal::toSixDecimals('0'));
    }

    private function assertSameDecimal(string $label, string $actual): void
    {
        // 1/3 — 0.333...; достаточно проверить, что начинается на 0.333333.
        self::assertStringStartsWith('0.333333', $actual);
        self::assertSame(22, strlen($actual)); // '0.' + 20 знаков
    }
}
