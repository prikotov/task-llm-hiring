<?php

declare(strict_types=1);

namespace App\Tests\Util;

use App\Exception\ImportException;
use App\Util\UtcTime;
use PHPUnit\Framework\TestCase;

final class UtcTimeTest extends TestCase
{
    public function testParsesZuluForm(): void
    {
        $dt = UtcTime::parse('2026-09-18T11:07:29Z', 'test');

        self::assertSame(0, $dt->getOffset());
        self::assertSame('2026-09-18', $dt->format('Y-m-d'));
        self::assertSame('11:07:29', $dt->format('H:i:s'));
    }

    public function testParsesExplicitZeroOffsetAndFractionalSeconds(): void
    {
        self::assertSame('2026-09-18T11:07:29', UtcTime::parse('2026-09-18T11:07:29+00:00', 't')->format('Y-m-d\TH:i:s'));
        self::assertSame('2026-09-18T11:07:29.500000', UtcTime::parse('2026-09-18T11:07:29.5Z', 't')->format('Y-m-d\TH:i:s.u'));
    }

    public function testRejectsNonUtcOffset(): void
    {
        $this->expectException(ImportException::class);
        UtcTime::parse('2026-09-18T13:07:29+02:00', 'test');
    }

    public function testRejectsGarbageString(): void
    {
        $this->expectException(ImportException::class);
        UtcTime::parse('not-a-date', 'test');
    }

    public function testRejectsNull(): void
    {
        $this->expectException(ImportException::class);
        UtcTime::parse(null, 'test');
    }

    public function testFormatProducesUtc(): void
    {
        self::assertSame('2026-09-18T11:07:29Z', UtcTime::format(UtcTime::parse('2026-09-18T11:07:29Z', 't')));
    }
}
