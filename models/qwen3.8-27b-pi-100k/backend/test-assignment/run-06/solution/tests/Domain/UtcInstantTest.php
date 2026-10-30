<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Time\UtcInstant;
use PHPUnit\Framework\TestCase;

final class UtcInstantTest extends TestCase
{
    public function testValidInstant(): void
    {
        $dt = UtcInstant::parse('2026-08-01T00:00:00Z');
        self::assertSame(0, $dt->getOffset());
        self::assertSame('2026-08-01T00:00:00', $dt->format('Y-m-d\TH:i:s'));
    }

    public function testFractionalSeconds(): void
    {
        $dt = UtcInstant::parse('2026-08-01T00:00:01.250Z');
        self::assertSame(1, (int) $dt->format('s'));
    }

    public function testRejectsNonUtcSuffix(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UtcInstant::parse('2026-08-01T02:00:00+02:00');
    }

    public function testRejectsSpaceSeparator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UtcInstant::parse('2026-08-01 00:00:00Z');
    }

    public function testRejectsDateOnly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UtcInstant::parse('2026-08-01');
    }

    public function testRejectsInvalidDate(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UtcInstant::parse('2026-02-30T00:00:00Z');
    }
}
