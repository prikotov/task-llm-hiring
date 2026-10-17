<?php

declare(strict_types=1);

namespace App\Tests;

use App\Billing\CostCalculator;
use App\Billing\OffPeak;
use App\Entity\PriceRow;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private static function at(string $iso): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $iso, new \DateTimeZone('UTC'));
    }

    private static function prismPrismRow(array $rates): PriceRow
    {
        return new PriceRow(
            1,
            self::at('2026-08-01T00:00:00Z'),
            'USD',
            'per_1m_tokens',
            $rates,
        );
    }

    public function testEventE1PeakTimeFromTaskExample(): void
    {
        $row = self::prismPrismRow(['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000']);
        $offPeak = new OffPeak('16:30', '00:30', '0.5');
        $result = (new CostCalculator())->calculate(
            $row,
            ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
            $offPeak,
            self::at('2026-09-15T11:00:00Z'),
        );

        self::assertSame('14.262499', $result['costUsd']);
        self::assertFalse($result['offPeakApplied']);
        self::assertSame(0, bccomp($result['baseCost'], '14.26249875', 20));
    }

    public function testEventE1OffPeakMultipliesWholeEvent(): void
    {
        $row = self::prismPrismRow(['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000']);
        $offPeak = new OffPeak('16:30', '00:30', '0.5');
        $result = (new CostCalculator())->calculate(
            $row,
            ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
            $offPeak,
            self::at('2026-09-15T17:10:00Z'),
        );

        self::assertTrue($result['offPeakApplied']);
        self::assertSame('7.131249', $result['costUsd']); // 14.26249875 × 0.5, одно округление
    }

    public function testEventE2PriceChangeBoundaryInclusive(): void
    {
        $row = new PriceRow(
            1,
            self::at('2026-09-15T12:00:00Z'),
            'USD',
            'per_1m_tokens',
            ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.150000', 'cache_write' => '1.875000'],
        );
        $result = (new CostCalculator())->calculate(
            $row,
            ['input' => 2000000, 'output' => 1000000, 'cache_read' => 0, 'cache_write' => 0],
            null,
            self::at('2026-09-15T12:00:00Z'), // valid_from включительно
        );

        self::assertSame('11.000000', $result['costUsd']);
        self::assertFalse($result['offPeakApplied']);
    }

    public function testEventE3UnifiedRate(): void
    {
        $row = new PriceRow(
            2,
            self::at('2026-08-01T00:00:00Z'),
            'USD',
            'per_1m_tokens_unified',
            ['any' => '2.400000'],
        );
        $result = (new CostCalculator())->calculate(
            $row,
            ['input' => 3000000, 'output' => 500000, 'cache_read' => 0, 'cache_write' => 0],
            null,
            self::at('2026-09-20T17:10:00Z'),
        );

        self::assertSame('8.400000', $result['costUsd']);
        self::assertFalse($result['offPeakApplied']);
    }

    public function testZeroTokensCostIsZero(): void
    {
        $row = self::prismPrismRow(['input' => '3', 'output' => '12', 'cache_read' => '0.3', 'cache_write' => '3.75']);
        $result = (new CostCalculator())->calculate(
            $row,
            ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            new OffPeak('16:30', '00:30', '0.5'),
            self::at('2026-09-15T17:00:00Z'),
        );

        self::assertSame('0.000000', $result['costUsd']);
        self::assertTrue($result['offPeakApplied']);
    }

    public function testUnknownUnitIsRejected(): void
    {
        $row = new PriceRow(1, self::at('2026-08-01T00:00:00Z'), 'USD', 'weird', ['input' => '1']);
        $this->expectException(\DomainException::class);
        (new CostCalculator())->calculate($row, ['input' => 1, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0], null, self::at('2026-09-15T00:00:00Z'));
    }
}
