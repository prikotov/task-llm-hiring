<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Domain\OffPeak;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    public function testParseWindow(): void
    {
        self::assertSame(0, OffPeak::parseWindow('00:00'));
        self::assertSame(900, OffPeak::parseWindow('15:00'));
        self::assertSame(990, OffPeak::parseWindow('16:30'));
        self::assertSame(1439, OffPeak::parseWindow('23:59'));
        $this->expectException(DomainException::class);
        OffPeak::parseWindow('5:5');
    }

    public function testWindowCrossingMidnight(): void
    {
        $op = new OffPeak(990, 30, '0.500000'); // 16:30 - 00:30
        self::assertTrue($op->contains(990));   // start включена
        self::assertTrue($op->contains(1020));  // 17:00
        self::assertTrue($op->contains(1439));  // 23:59
        self::assertTrue($op->contains(0));     // 00:00
        self::assertTrue($op->contains(29));    // 00:29
        self::assertFalse($op->contains(30));   // end эксклюзивна
        self::assertFalse($op->contains(60));   // 01:00
        self::assertFalse($op->contains(989));  // 16:29
    }

    public function testWindowSameDay(): void
    {
        $op = new OffPeak(600, 1200, '0.500000'); // 10:00 - 20:00
        self::assertTrue($op->contains(600));
        self::assertTrue($op->contains(1199));
        self::assertFalse($op->contains(1200));
        self::assertFalse($op->contains(599));
    }

    public function testWindowFullDayDegenerate(): void
    {
        $op = new OffPeak(0, 0, '0.500000'); // вырожденное окно == полный день
        self::assertTrue($op->contains(0));
        self::assertTrue($op->contains(1439));
    }

    public function testNotConfigured(): void
    {
        $op = new OffPeak(null, null, null);
        self::assertFalse($op->isConfigured());
        self::assertFalse($op->contains(100));
        self::assertNull($op->minutesStart());
        self::assertNull($op->minutesEnd());
        self::assertNull($op->multiplier());
    }

    public function testMinutesOf(): void
    {
        self::assertSame(990, OffPeak::minutesOf(new \DateTimeImmutable('2026-09-16T16:30:00Z', new \DateTimeZone('UTC'))));
        self::assertSame(0, OffPeak::minutesOf(new \DateTimeImmutable('2026-09-16T00:00:00Z', new \DateTimeZone('UTC'))));
        self::assertSame(29, OffPeak::minutesOf(new \DateTimeImmutable('2026-09-16T00:29:59Z', new \DateTimeZone('UTC'))));
    }
}
