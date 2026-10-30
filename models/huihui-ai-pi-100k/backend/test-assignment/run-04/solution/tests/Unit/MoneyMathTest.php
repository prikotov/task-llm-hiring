<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\MoneyMath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyMathTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function roundHalfUpProvider(): array
    {
        return [
            'zero' => ['0', '0.000000'],
            'no_fraction' => ['2.5', '2.500000'],
            'exact_fixture_total' => ['14.26249875', '14.262499'],
            'round_down' => ['0.0000004', '0.000000'],
            'half_rounds_up' => ['0.0000005', '0.000001'],
            'high_precise_round_down' => ['1234567.890123499', '1234567.890123'],
            'high_precise_round_up' => ['1234567.890123500', '1234567.890124'],
            'beyond_scale' => ['1234567.8901235', '1234567.890124'],
            'integer_input' => ['15', '15.000000'],
        ];
    }

    #[DataProvider('roundHalfUpProvider')]
    public function testRoundHalfUp6(string $value, string $expected): void
    {
        self::assertSame($expected, MoneyMath::roundHalfUp6($value));
    }

    public function testAddSubMulDivKeepScale16(): void
    {
        self::assertSame('3.5000000000000000', MoneyMath::add('1', '2.5'));
        self::assertSame('-0.5000000000000000', MoneyMath::sub('1', '1.5'));
        self::assertSame('3600000.0000000000000000', MoneyMath::mul('1200000', '3.000000'));
        self::assertSame('3.6000000000000000', MoneyMath::div('3600000', '1000000'));
        self::assertSame('3.6000000000000000', MoneyMath::div(MoneyMath::mul('1200000', '3.000000'), '1000000'));
    }

    public function testFormat6(): void
    {
        self::assertSame('15.000000', MoneyMath::format6('15'));
        self::assertSame('14.262499', MoneyMath::format6('14.262499'));
        self::assertSame('14.262499', MoneyMath::format6('14.262499000000'));
        self::assertSame('0.000000', MoneyMath::format6('-0.000000'));
    }

    public function testNoFloatInvolvement(): void
    {
        // 0.1 + 0.2 в float даёт 0.30000000000000004; у нас — ровно 0.3.
        self::assertSame('0.3000000000000000', MoneyMath::add('0.1', '0.2'));
    }
}
