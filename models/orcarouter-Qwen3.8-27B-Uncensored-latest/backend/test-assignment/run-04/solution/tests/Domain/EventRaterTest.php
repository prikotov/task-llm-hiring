<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Pricing\EventRater;
use App\Domain\Pricing\OffPeak;
use App\Domain\Pricing\PriceEntry;
use App\Domain\Pricing\UsageRecord;
use PHPUnit\Framework\TestCase;

final class EventRaterTest extends TestCase
{
    public function testFixtureEventE1PeaKHours(): void
    {
        // 2026-09-15T11:00 UTC is peak hours for prism (off-peak 16:30–00:30)
        $usage = new UsageRecord(1200000, 800000, 2500000, 83333, new \DateTimeImmutable('2026-09-15T11:00:00+00:00'));

        $price = new PriceEntry(PriceEntry::UNIT_PER_TOKEN, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $cost = (new EventRater())->rate($usage, $price, new OffPeak('16:30', '00:30', '0.5'));

        self::assertSame('14.262499', $cost);
    }

    public function testOffPeakApplied(): void
    {
        $usage = new UsageRecord(1000000, 0, 0, 0, new \DateTimeImmutable('2026-09-15T17:00:00+00:00'));
        $price = new PriceEntry(PriceEntry::UNIT_PER_TOKEN, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $cost = (new EventRater())->rate($usage, $price, new OffPeak('16:30', '00:30', '0.5'));

        self::assertSame('1.500000', $cost);
    }

    public function testUnifiedScheme(): void
    {
        $usage = new UsageRecord(3000000, 500000, 0, 0, new \DateTimeImmutable('2026-09-20T17:10:00+00:00'));
        $price = new PriceEntry(PriceEntry::UNIT_UNIFIED, ['any' => '2.400000']);

        // sfera has no off-peak: time of day is irrelevant
        self::assertSame('8.400000', (new EventRater())->rate($usage, $price, null));
    }

    public function testZeroTokens(): void
    {
        $usage = new UsageRecord(0, 0, 0, 0, new \DateTimeImmutable('2026-09-20T17:10:00+00:00'));
        $price = new PriceEntry(PriceEntry::UNIT_UNIFIED, ['any' => '2.400000']);

        self::assertSame('0.000000', (new EventRater())->rate($usage, $price, null));
    }

    public function testSchemeMismatchRejected(): void
    {
        $this->expectException(\App\Shared\ImportException::class);

        new PriceEntry(PriceEntry::UNIT_PER_TOKEN, ['input' => '3.000000']);
    }
}
