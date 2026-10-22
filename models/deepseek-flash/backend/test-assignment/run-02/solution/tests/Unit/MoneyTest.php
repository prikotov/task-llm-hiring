<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\Money\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[DataProvider('roundingProvider')]
    public function testRoundHalfUp(string $value, string $expected): void
    {
        self::assertSame($expected, Money::roundHalfUp($value, 6));
    }

    /** @return iterable<string, array{string, string}> */
    public static function roundingProvider(): iterable
    {
        yield 'fixture event' => ['14.26249875', '14.262499'];
        yield 'half up' => ['0.0000025', '0.000003'];
        yield 'below half' => ['0.000002499', '0.000002'];
        yield 'exact' => ['8.400000', '8.400000'];
        yield 'negative half up' => ['-0.0000025', '-0.000003'];
        yield 'carry' => ['1.9999999', '2.000000'];
    }

    public function testArithmeticDoesNotUseFloats(): void
    {
        $sum = Money::add('0.100000', '0.200000', 6);

        self::assertSame('0.300000', $sum);
    }
}
