<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeakPolicy;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class OffPeakPolicyTest extends TestCase
{
    private static function at(string $hhmm): DateTimeImmutable
    {
        return (new DateTimeImmutable('2026-01-01 00:00:00 UTC'))->setTime((int) substr($hhmm, 0, 2), (int) substr($hhmm, 3, 2));
    }

    public function testDayWindow(): void
    {
        // Окно [start, end) минут — end исключительно (по спецификации).
        $p = OffPeakPolicy::fromMinutes(16 * 60, 23 * 60 + 59, '0.5');

        self::assertFalse($p->covers(self::at('15:59')));
        self::assertTrue($p->covers(self::at('16:00')));
        self::assertTrue($p->covers(self::at('19:00')));
        self::assertTrue($p->covers(self::at('23:58')));
        self::assertFalse($p->covers(self::at('23:59')));
        self::assertFalse($p->covers(self::at('00:00')));
    }

    public function testCrossMidnight(): void
    {
        $p = OffPeakPolicy::fromMinutes(22 * 60 + 30, 1 * 60 + 30, '0.9');

        self::assertFalse($p->covers(self::at('22:29')));
        self::assertTrue($p->covers(self::at('22:30')));
        self::assertTrue($p->covers(self::at('00:15')));
        self::assertTrue($p->covers(self::at('01:29')));
        self::assertFalse($p->covers(self::at('01:30')));
    }

    public function testMultipliersAndEmptyWindow(): void
    {
        $p = OffPeakPolicy::fromMinutes(12 * 60, 12 * 60, '0.5'); // пустое окно [12:00, 12:00)
        self::assertSame('0.5', $p->multiplier());
        self::assertFalse($p->covers(self::at('12:00')));
    }

    public function testFromSpec(): void
    {
        $p = OffPeakPolicy::fromSpec([
            'window_start' => '16:00',
            'window_end' => '23:59',
            'multiplier' => '0.5',
        ]);

        self::assertTrue($p->covers(self::at('16:00')));
        self::assertTrue($p->covers(self::at('23:58')));
        self::assertFalse($p->covers(self::at('23:59')), 'window_end исключительно (по спецификации)');
        self::assertFalse($p->covers(self::at('15:00')));
    }

    public function testFromSpecRejectsBadValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        OffPeakPolicy::fromSpec(['window_start' => '25:00', 'window_end' => '23:59', 'multiplier' => '0.5']);
    }
}
