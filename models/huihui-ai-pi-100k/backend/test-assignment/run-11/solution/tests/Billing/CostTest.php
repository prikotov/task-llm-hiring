<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Billing\Cost;
use App\Domain\DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CostTest extends TestCase
{
    /**
     * @return array<string, array{int: string, int: string, int: string}>
     */
    public static function addProvider(): array
    {
        return [
            'simple integer' => ['1', '2', '3'],
            'decimal with trailing zero' => ['1.5', '2.25', '3.75'],
            'large number' => ['99999999.999999', '0.000001', '100000000'],
            'negative zero' => ['-0', '5', '5'],
        ];
    }

    #[DataProvider('addProvider')]
    public function testAddIsExact(string $a, string $b, string $expected): void
    {
        self::assertSame($expected, Cost::make($a)->add(Cost::make($b))->decimal());
    }

    public function testSubtractIsExact(): void
    {
        self::assertSame(
            '3.5',
            Cost::make('4.5')->subtract(Cost::make('1.0'))->decimal(),
        );
    }

    public function testMultiplyIsExact(): void
    {
        self::assertSame(
            '4.5',
            Cost::make('3.0')->multiply(Cost::make('1.5'))->decimal(),
        );
    }

    public function testSumOfZerosIsZero(): void
    {
        self::assertTrue(Cost::zero()->add(Cost::zero())->equalsZero());
    }

    /**
     * @return array<string, array{int: string, int: string}>
     */
    public static function roundHalfUp6Cases(): array
    {
        return [
            'exact six decimals stays' => ['3.141592', '3.141592'],
            '7th decimal half-up (0.0000005 → 0.000001)' => ['0.0000005', '0.000001'],
            '8th decimal 8 -> round up' => ['1.2345678', '1.234568'],
            'large number stays' => ['14.262499', '14.262499'],
        ];
    }

    #[DataProvider('roundHalfUp6Cases')]
    public function testRoundHalfUp6(string $x, string $expected): void
    {
        self::assertSame($expected, Cost::roundHalfUp6(Cost::make($x))->decimal());
    }

    #[DataProvider('roundHalfUp6Cases')]
    public function testRoundHalfUp6ToString(string $x, string $expected): void
    {
        self::assertSame($expected, Cost::roundHalfUp6(Cost::make($x))->toSixDecimalsTruncated());
    }

    public function testRejectsNonDecimal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cost::make('abc');
    }

    public function testRejectsNegativeFloatNotation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cost::make('1e5');
    }
}
