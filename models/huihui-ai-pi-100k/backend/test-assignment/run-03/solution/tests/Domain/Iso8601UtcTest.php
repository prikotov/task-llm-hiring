<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Common\Iso8601Utc;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class Iso8601UtcTest extends TestCase
{
    public function testZuluSuffix(): void
    {
        $utc = Iso8601Utc::parse('2026-09-15T11:00:00Z');
        self::assertSame('2026-09-15 11:00:00', $utc->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $utc->getTimezone()->getName());
    }

    public function testExplicitOffsetIsNormalizedToUtc(): void
    {
        $plus = Iso8601Utc::parse('2026-09-15T16:30:00+05:30');
        self::assertSame('2026-09-15 11:00:00', $plus->format('Y-m-d H:i:s'));

        $minus = Iso8601Utc::parse('2026-09-15T05:30:00-04:30');
        self::assertSame('2026-09-15 10:00:00', $minus->format('Y-m-d H:i:s'));

        // Смещение без двоеточия также допустимо в ISO-8601.
        $compact = Iso8601Utc::parse('2026-09-15T16:30:00+0530');
        self::assertSame('2026-09-15 11:00:00', $compact->format('Y-m-d H:i:s'));
    }

    public function testSecondLessTimestamp(): void
    {
        $utc = Iso8601Utc::parse('2026-09-15T11:00Z');
        self::assertSame('11:00:00', $utc->format('H:i:s'));
    }

    public function testInvalidDatesAreRejected(): void
    {
        $cases = [
            '2026-02-30T11:00:00Z',  // 30 февраля
            '2026-09-15T25:00:00Z',  // 25-й час
            '2026-13-01T00:00:00Z',
            '2026-09-15 11:00:00',   // нет часового пояса
            '2026-09-15',
            '15.09.2026 11:00:00Z',
            '',
        ];

        foreach ($cases as $case) {
            try {
                Iso8601Utc::parse($case);
                self::fail(sprintf('Ожидалось исключение для "%s"', $case));
            } catch (InvalidArgumentException) {
                // ожидаемо
            }
        }

        self::assertCount(7, $cases);
    }

    public function testReanchorKeepsTheSameInstant(): void
    {
        $utc = new \DateTimeImmutable('@1786500000', new DateTimeZone('UTC'));
        self::assertSame('1786500000', (string) Iso8601Utc::reanchor($utc)->getTimestamp());
        self::assertSame('UTC', Iso8601Utc::reanchor($utc)->getTimezone()->getName());
    }
}
