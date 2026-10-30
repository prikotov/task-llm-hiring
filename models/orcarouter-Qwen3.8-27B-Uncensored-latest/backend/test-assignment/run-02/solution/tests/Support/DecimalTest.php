<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Support\Decimal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Тестирует точную арифметику: HALF_UP-округление до 6 знаков,
 * нормализация формата — в полном соответствии с правилом п. 4 задания.
 */
final class DecimalTest extends TestCase
{
    #[DataProvider('roundMoneyProvider')]
    public function test_round_money_half_up_to_6dp(string $input, string $expected): void
    {
        self::assertSame($expected, Decimal::roundMoney($input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function roundMoneyProvider(): array
    {
        return [
            'e1 exact'      => ['14.26249875', '14.262499'],   // 7-я цифра 7 -> округление вверх
            'e2 integer'    => ['11.0', '11.000000'],
            'e3 unified'    => ['8.4', '8.400000'],
            'zero'          => ['0', '0.000000'],
            'half up'       => ['1.0000005', '1.000001'],       // ровно половина -> вверх
            'half down side' => ['1.0000004', '1.000000'],      // меньше половины -> вниз
            'large'         => ['1234567.8910004', '1234567.891000'],
        ];
    }

    public function test_format_money_normalizes_to_six_dp(): void
    {
        self::assertSame('8.400000', Decimal::formatMoney('8.4'));
        self::assertSame('33.662499', Decimal::formatMoney('33.662499'));
        self::assertSame('11.000000', Decimal::formatMoney('11'));
    }

    public function test_multiplication_keeps_precision(): void
    {
        // 11.0 * 0.5 = 5.5 — точное, без float
        self::assertSame('5.50000000000000000000', Decimal::mul('11', '0.5'));
    }
}
