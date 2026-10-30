<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[DataProvider('roundCases')]
    public function testRoundHalfUp(string $value, string $expected): void
    {
        self::assertSame($expected, Money::roundHalfUp($value, 6));
    }

    public static function roundCases(): iterable
    {
        // Примеры из задания: 2.5 -> 3 (для scale 0), точные стоимости событий.
        yield 'e1' => ['14.26249875', '14.262499'];
        yield 'e2' => ['11.0', '11.000000'];
        yield 'e3' => ['8.4', '8.400000'];
        yield 'half-up' => ['2.5', '2.500000'];
        yield 'half-up-scale0-like' => ['14.2624985', '14.262499'];
        yield 'down' => ['14.2624984', '14.262498'];
        yield 'zero' => ['0', '0.000000'];
        yield 'carry' => ['9.9999999', '10.000000'];
        yield 'exact6' => ['0.31249875', '0.312499'];
    }

    public function testToSixFormatsTrailingZeros(): void
    {
        self::assertSame('8.400000', Money::toSix('8.4'));
        self::assertSame('11.000000', Money::toSix('11'));
        self::assertSame('0.000000', Money::toSix('0'));
    }
}
