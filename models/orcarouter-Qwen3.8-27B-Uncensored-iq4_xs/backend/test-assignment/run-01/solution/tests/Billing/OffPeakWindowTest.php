<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\OffPeakWindow;
use App\Exception\ImportException;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    /** Окно из задания: 16:30 — 00:30 (переход через полночь). */
    private static function midnightWindow(): OffPeakWindow
    {
        return new OffPeakWindow(16 * 60 + 30, 30, '0.5');
    }

    public function testFromSpec(): void
    {
        $window = OffPeakWindow::fromSpec(
            ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0.5'],
            'test',
        );

        self::assertNotNull($window);
        self::assertSame('0.5', $window->multiplier());
        self::assertSame(990, $window->startMinutes());
        self::assertSame(30, $window->endMinutes());
    }

    public function testMidnightWindowBoundaries(): void
    {
        $window = self::midnightWindow();

        self::assertFalse($window->isInside(self::ts('2026-09-15T16:29:59')));
        self::assertTrue($window->isInside(self::ts('2026-09-15T16:30:00')));  // start — включительно
        self::assertTrue($window->isInside(self::ts('2026-09-15T23:59:59')));
        self::assertTrue($window->isInside(self::ts('2026-09-16T00:00:00')));
        self::assertTrue($window->isInside(self::ts('2026-09-16T00:29:59')));
        self::assertFalse($window->isInside(self::ts('2026-09-16T00:30:00'))); // end — исключаемо
        self::assertFalse($window->isInside(self::ts('2026-09-16T09:00:00')));
    }

    public function testNonWrappingWindow(): void
    {
        $window = new OffPeakWindow(9 * 60, 17 * 60, '0.5');

        self::assertFalse($window->isInside(self::ts('2026-09-16T08:59:59')));
        self::assertTrue($window->isInside(self::ts('2026-09-16T09:00:00')));
        self::assertTrue($window->isInside(self::ts('2026-09-16T16:59:59')));
        self::assertFalse($window->isInside(self::ts('2026-09-16T17:00:00')));
        self::assertFalse($window->isInside(self::ts('2026-09-16T23:59:59')));
    }

    public function testEqualStartAndEndIsEmptyWindow(): void
    {
        $window = new OffPeakWindow(9 * 60, 9 * 60, '0.5');

        self::assertFalse($window->isInside(self::ts('2026-09-16T09:30:00')));
    }

    public function testMultiplierMustBePositiveDecimal(): void
    {
        $this->expectException(ImportException::class);
        OffPeakWindow::fromSpec(
            ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0'],
            'test',
        );
    }

    public function testStartEqualsEndSpecIsEmptyNotFullDay(): void
    {
        $window = OffPeakWindow::fromSpec(
            ['window_start' => '09:00', 'window_end' => '09:00', 'multiplier' => '0.5'],
            'test',
        );

        self::assertNotNull($window);
        self::assertFalse($window->isInside(self::ts('2026-09-16T12:00:00')));
    }

    private static function ts(string $iso): DateTimeImmutable
    {
        return new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }
}
