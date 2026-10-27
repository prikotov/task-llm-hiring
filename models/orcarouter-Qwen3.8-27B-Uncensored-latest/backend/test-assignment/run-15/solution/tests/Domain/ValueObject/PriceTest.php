<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use App\Domain\ValueObject\Price;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    private static function value(string $n): string
    {
        if (!\str_contains($n, '.')) return $n;
        return \rtrim(\rtrim($n, '0'), '.');
    }

    public function test_per1mSeparateRates(): void
    {
        $price = Price::fromPayload([
            'unit' => 'per_1m_tokens',
            'rates' => [
                'input'       => '3.000000',
                'output'      => '12.000000',
                'cache_read'  => '0.300000',
                'cache_write' => '3.750000',
            ],
        ]);

        // 1.2M in + 0.8M out + 0 cr + 0 cw = 1.2*3 + 0.8*12 = 3.6+9.6 = 13.2
        $cost = $price->costFor(1_200_000, 800_000, 0, 0);
        self::assertSame('13.200000', $cost->roundTo(6)->format(6));
    }

    public function test_per1mSeparateRatesSpecExample(): void
    {
        $price = Price::fromPayload([
            'unit' => 'per_1m_tokens',
            'rates' => [
                'input'       => '3.000000',
                'output'      => '12.000000',
                'cache_read'  => '0.300000',
                'cache_write' => '3.750000',
            ],
        ]);

        // spec example e1: 1.2M in, 0.8M out, 2.5M cr, 83333 cw
        // = 3.6 + 9.6 + 0.75 + 0.31249875 = 14.26249875 -> 14.262499
        $cost = $price->costFor(1_200_000, 800_000, 2_500_000, 83_333);
        self::assertSame('14.262499', $cost->roundTo(6)->format(6));
    }

    public function test_per1mUnifiedRates(): void
    {
        $price = Price::fromPayload([
            'unit' => 'per_1m_tokens_unified',
            'rates' => ['any' => '2.400000'],
        ]);

        // 3.5M total x 2.4 / 1M = 8.4
        $cost = $price->costFor(3_000_000, 500_000, 0, 0);
        self::assertSame('8.400000', $cost->roundTo(6)->format(6));
    }

    public function test_zeroTokens(): void
    {
        $price = Price::fromPayload([
            'unit' => 'per_1m_tokens',
            'rates' => [
                'input' => '1.000000', 'output' => '1.000000',
                'cache_read' => '1.000000', 'cache_write' => '1.000000',
            ],
        ]);
        self::assertSame('0', self::value($price->costFor(0, 0, 0, 0)->toString()));
    }

    public function test_invalidUnitThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Price::fromPayload(['unit' => 'bogus', 'rates' => ['any' => '1']]);
    }

    public function test_per1mMissingRateThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Price::fromPayload([
            'unit' => 'per_1m_tokens',
            'rates' => ['input' => '1', 'output' => '1', 'cache_read' => '1'],
        ]);
    }
}
