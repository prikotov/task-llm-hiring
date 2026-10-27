<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\TimeWindow;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TimeWindowTest extends TestCase
{
    private static function at(string $hhMm): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-02-01T'.$hhMm.':00+00:00');
    }

    #[DataProvider('sameDayProvider')]
    public function testSameDayWindow(string $hhMm, bool $expected): void
    {
        self::assertSame($expected, TimeWindow::contains(self::at($hhMm), '09:00', '17:00'));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function sameDayProvider(): array
    {
        return [
            'before_start' => ['08:59', false],
            'start_inclusive' => ['09:00', true],
            'middle' => ['12:34', true],
            'before_end' => ['16:59', true],
            'end_exclusive' => ['17:00', false],
            'after_end' => ['17:01', false],
            'next_day' => ['09:01', true],
        ];
    }

    #[DataProvider('midnightCrossingProvider')]
    public function testMidnightCrossing(string $hhMm, bool $expected): void
    {
        self::assertSame($expected, TimeWindow::contains(self::at($hhMm), '23:00', '01:00'));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function midnightCrossingProvider(): array
    {
        return [
            'just_before_start' => ['22:59', false],
            'start_inclusive' => ['23:00', true],
            'midnight' => ['00:00', true],
            'before_end' => ['00:59', true],
            'end_exclusive' => ['01:00', false],
            'after_end' => ['01:01', false],
            'far_midday' => ['13:00', false],
            'late_night' => ['23:30', true],
        ];
    }

    public function testEmptyWindowIsNeverContained(): void
    {
        self::assertFalse(TimeWindow::contains(self::at('05:00'), '05:00', '05:00'));
        self::assertFalse(TimeWindow::contains(self::at('23:00'), '00:00', '00:00'));
    }

    public function testInputConvertedToUtc(): void
    {
        // 16:30+05:30 == 11:00 UTC — внутри окна 09:00–12:00
        $local = new DateTimeImmutable('2026-02-01T16:30:00+05:30')->setTimezone(new DateTimeZone('UTC'));

        self::assertTrue(TimeWindow::contains($local, '09:00', '12:00'));
    }
}
