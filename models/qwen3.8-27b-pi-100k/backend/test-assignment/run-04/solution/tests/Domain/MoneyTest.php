<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    /**
     * @param string $raw  точное значение (без округления)
     * @param string $exp  ожидание после toScale6
     */
    #[DataProvider('roundingProvider')]
    public function test_toScale6_half_up(string $raw, string $exp): void
    {
        self::assertSame($exp, Money::toScale6($raw));
    }

    public static function roundingProvider(): array
    {
        return [
            'нет округления'          => ['8.4', '8.400000'],
            'нормализация знаков'     => ['14.26', '14.260000'],
            'HALF_UP вверх'           => ['2.0000005', '2.000001'],
            'HALF_UP вниз'            => ['1.00000049999', '1.000000'],
            'пример из задания'       => ['14.26249875', '14.262499'],
            'отрицательное, от нуля'  => ['-0.1234567', '-0.123457'],
            'минус ноль'              => ['-0.0000004', '0.000000'],
            'большие значения'        => ['12345678.0000015', '12345678.000002'],
            'целое'                   => ['0', '0.000000'],
        ];
    }

    public function test_add_precise(): void
    {
        self::assertSame('0.300000000000000000000000', Money::add('0.1', '0.2'));
        self::assertSame('33.662499', Money::toScale6(Money::add(Money::add('14.262499', '8.4'), '11.0')));
    }

    public function test_tokens_at_rate(): void
    {
        // 83333 токена × 3.75 / 1 000 000 = 0.31249875
        self::assertSame('0.312498750000000000000000', Money::tokensAtRate(83_333, '3.75'));
    }

    public function test_scale_is_at_least_16(): void
    {
        self::assertGreaterThanOrEqual(16, Money::SCALE);
    }
}
