<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Shared\Exception\BadInputException;
use App\Support\StrictIsoUtc;
use PHPUnit\Framework\TestCase;

final class StrictIsoUtcTest extends TestCase
{
    public function testParsesCanonicalUtc(): void
    {
        $dt = StrictIsoUtc::parse('2026-09-15T11:00:00Z');
        self::assertSame('2026-09-15 11:00:00', $dt->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $dt->getTimezone()->getName());
        self::assertSame('2026-09-15T11:00:00Z', StrictIsoUtc::format($dt));
    }

    public function testParsesFractionalSecondsUpToMicros(): void
    {
        $dt = StrictIsoUtc::parse('2026-07-08T09:15:30.250Z');
        self::assertSame('250000', $dt->format('u'));
    }

    public function testRejectsNonExistentCalendarDay(): void
    {
        $this->expectException(BadInputException::class);
        StrictIsoUtc::parse('2026-02-30T00:00:00Z');
    }

    public function testRejectsMissingZSuffix(): void
    {
        $this->expectException(BadInputException::class);
        StrictIsoUtc::parse('2026-09-15T11:00:00');
    }

    public function testRejectsExplicitOffsetInsteadOfZ(): void
    {
        $this->expectException(BadInputException::class);
        StrictIsoUtc::parse('2026-09-15T11:00:00+00:00');
    }

    public function testRejectsFractionLongerThanSixDigits(): void
    {
        $this->expectException(BadInputException::class);
        StrictIsoUtc::parse('2026-09-15T11:00:00.1234567Z');
    }

    public function testRejectsSecondSixty(): void
    {
        $this->expectException(BadInputException::class);
        StrictIsoUtc::parse('2026-09-15T11:00:60Z');
    }

    public function testRejectsHourTwentyFour(): void
    {
        $this->expectException(BadInputException::class);
        StrictIsoUtc::parse('2026-09-15T24:00:00Z');
    }
}
