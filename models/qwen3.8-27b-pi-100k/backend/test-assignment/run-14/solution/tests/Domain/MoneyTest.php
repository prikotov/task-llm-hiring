<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Pricing\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public static function provideRoundHalfUp(): iterable
    {
        yield 'half-up round' => ['1.234565', '1.234565'];
        yield 'half-up round up' => ['1.2345649999999', '1.234565'];
        yield 'half-up round to 0' => ['1.234564', '1.234564'];
        yield 'negative half-up' => ['-1.234565', '-1.234565'];
        yield 'no carry' => ['1.9999994', '1.999999'];
        yield 'round up to 1' => ['0.9999995', '1.000000'];
        yield 'round up to 2' => ['1.9999995', '2.000000'];
        yield 'exact half rounds up' => ['2.5', '2.500000'];
        yield 'zero' => ['0', '0.000000'];
    }

    #[DataProvider('provideRoundHalfUp')]
    public function testRoundTo6HalfUp(string $raw, string $expected): void
    {
        $money = new Money($raw);
        $this->assertSame($expected, $money->roundTo6()->format());
    }

    public function testAddSubMulDiv(): void
    {
        $a = new Money('1.5');
        $b = new Money('2.25');

        $this->assertSame('3.750000', $a->add($b)->format());
        $this->assertSame('0.750000', $b->subtract($a)->format());
        $this->assertSame('3.000000', $a->multiplyBy('2')->format());
        $this->assertSame('0.750000', $a->divideBy('2')->format());
    }

    public function testIsZero(): void
    {
        $this->assertTrue((new Money('0'))->isZero());
        $this->assertFalse((new Money('0.000001'))->isZero());
    }

    public function testInvalidAmount(): void
    {
        $this->expectException(\RuntimeException::class);
        new Money('abc');
    }
}
