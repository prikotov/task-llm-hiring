<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Catalog\OffPeak;
use App\Shared\InputFormatException;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    public function testWindowInsideDay(): void
    {
        $offPeak = new OffPeak('09:00', '12:00', '0.5');
        self::assertFalse($offPeak->applies(self::ts('2026-01-01T08:59:00Z')));
        self::assertTrue($offPeak->applies(self::ts('2026-01-01T09:00:00Z')));
        self::assertTrue($offPeak->applies(self::ts('2026-01-01T11:59:59Z')));
        self::assertFalse($offPeak->applies(self::ts('2026-01-01T12:00:00Z')));
    }

    public function testWindowCrossingMidnight(): void
    {
        $offPeak = new OffPeak('16:30', '00:30', '0.5');
        self::assertFalse($offPeak->applies(self::ts('2026-01-01T16:29:00Z')));
        self::assertTrue($offPeak->applies(self::ts('2026-01-01T16:30:00Z')));
        self::assertTrue($offPeak->applies(self::ts('2026-01-01T23:59:00Z')));
        self::assertTrue($offPeak->applies(self::ts('2026-01-02T00:00:00Z')));
        self::assertTrue($offPeak->applies(self::ts('2026-01-02T00:29:59Z')));
        self::assertFalse($offPeak->applies(self::ts('2026-01-02T00:30:00Z')));
    }

    public function testEmptyWindowAppliesNowhere(): void
    {
        $offPeak = new OffPeak('10:00', '10:00', '0.5');
        self::assertFalse($offPeak->applies(self::ts('2026-01-01T10:00:00Z')));
    }

    public function testInvalidWindowFormatRejected(): void
    {
        $this->expectException(InputFormatException::class);
        new OffPeak('24:00', '00:30', '0.5');
    }

    public function testMultiplierIsExposed(): void
    {
        self::assertSame('0.5', (new OffPeak('16:30', '00:30', '0.5'))->multiplier());
    }

    private static function ts(string $iso): \DateTimeImmutable
    {
        return new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
    }
}
