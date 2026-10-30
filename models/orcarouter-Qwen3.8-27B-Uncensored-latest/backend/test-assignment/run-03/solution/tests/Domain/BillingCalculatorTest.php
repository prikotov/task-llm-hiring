<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\BillingCalculator;
use App\Domain\OffPeak;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class BillingCalculatorTest extends TestCase
{
    private const RATES_PER_KIND = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    private function ts(string $iso): DateTimeImmutable
    {
        return new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }

    /**
     * @param array<string, string> $rates
     */
    private function cost(
        int $in,
        int $out,
        int $cacheRead,
        int $cacheWrite,
        string $unit,
        array $rates,
        ?OffPeak $off,
        string $when,
    ): string {
        return (new BillingCalculator())->calculate(
            $in,
            $out,
            $cacheRead,
            $cacheWrite,
            $unit,
            $rates,
            $off,
            $this->ts($when),
        );
    }

    public function testEventE1PerTokenPeak(): void
    {
        $cost = $this->cost(
            1200000,
            800000,
            2500000,
            83333,
            BillingCalculator::UNIT_PER_TOKEN,
            self::RATES_PER_KIND,
            null,
            '2026-09-15T11:00:00Z',
        );

        self::assertSame('14.262499', $cost);
    }

    public function testOffPeakDiscountDoublesAsExpected(): void
    {
        $base = fn (?OffPeak $off, string $when): string => $this->cost(
            1200000,
            800000,
            2500000,
            83333,
            BillingCalculator::UNIT_PER_TOKEN,
            self::RATES_PER_KIND,
            $off,
            $when,
        );
        $off = new OffPeak('16:30', '00:30', '0.5');

        self::assertSame('14.262499', $base(null, '2026-09-15T11:00:00Z'));
        self::assertSame('7.131249', $base($off, '2026-09-15T17:00:00Z'));
    }

    public function testUnifiedRate(): void
    {
        $cost = $this->cost(
            3000000,
            500000,
            0,
            0,
            BillingCalculator::UNIT_PER_TOKEN_UNIFIED,
            ['any' => '2.400000'],
            null,
            '2026-09-20T17:10:00Z',
        );

        self::assertSame('8.400000', $cost);
    }

    public function testAllZeroTokensIsZero(): void
    {
        $cost = $this->cost(
            0,
            0,
            0,
            0,
            BillingCalculator::UNIT_PER_TOKEN,
            self::RATES_PER_KIND,
            null,
            '2026-09-15T11:00:00Z',
        );

        self::assertSame('0.000000', $cost);
    }
}
