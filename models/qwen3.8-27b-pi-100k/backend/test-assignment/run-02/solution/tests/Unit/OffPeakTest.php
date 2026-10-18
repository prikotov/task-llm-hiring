<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Billing\OffPeak\OffPeakPricing;
use App\Billing\OffPeak\OffPeakWindow;
use PHPUnit\Framework\TestCase;

/**
 * Границы окна: начало включительно, конец исключительный (TASK.md).
 * Окно может переходить через полночь; start == end — окно пустое.
 */
final class OffPeakTest extends TestCase
{
    public function testWindowCrossingMidnight(): void
    {
        // Окно провайдера prism из фикстур: 16:30–00:30.
        $window = new OffPeakWindow(16 * 60 + 30, 30);

        self::assertTrue($window->contains(16 * 60 + 30)); // 16:30 — начало включено
        self::assertTrue($window->contains(23 * 60 + 40)); // 23:40
        self::assertTrue($window->contains(0)); // 00:00 — уже ночь после окна
        self::assertTrue($window->contains(29)); // 00:29
        self::assertFalse($window->contains(30)); // 00:30 — конец исключён
        self::assertFalse($window->contains(16 * 60 + 29)); // 16:29 — до начала
        self::assertFalse($window->contains(12 * 60)); // 12:00 — пиковое время
    }

    public function testSameDayWindow(): void
    {
        // Гипотетическое окно 06:00–18:30.
        $window = new OffPeakWindow(6 * 60, 18 * 60 + 30);

        self::assertTrue($window->contains(6 * 60)); // 06:00 включено
        self::assertTrue($window->contains(18 * 60 + 29)); // 18:29
        self::assertFalse($window->contains(18 * 60 + 30)); // 18:30 исключено
        self::assertFalse($window->contains(6 * 60 - 1)); // 05:59
        self::assertFalse($window->contains(22 * 60)); // 22:00
    }

    public function testEmptyWindowWhenStartEqualsEnd(): void
    {
        // Допущение: пустое окно — скидка не применяется вообще.
        self::assertSame(null, OffPeakPricing::multiplierAt('00:00', '00:00', '0.500000', 0));
        self::assertSame(null, OffPeakPricing::multiplierAt('09:00', '09:00', '0.500000', 9 * 60));
        self::assertSame(false, (new OffPeakWindow(12 * 60, 12 * 60))->contains(12 * 60));
    }

    public function testNoOffPeakWhenMultiplierAbsent(): void
    {
        self::assertSame(null, OffPeakPricing::multiplierAt('16:30', '00:30', null, 1000));
        self::assertSame(null, OffPeakPricing::multiplierAt(null, '00:30', '0.500000', 1000));
        self::assertSame(null, OffPeakPricing::multiplierAt('16:30', null, '0.500000', 1000));
    }

    public function testMultiplierReturnedForEventsInsideWindow(): void
    {
        self::assertSame('0.500000', OffPeakPricing::multiplierAt('16:30', '00:30', '0.500000', 990));
        self::assertNull(OffPeakPricing::multiplierAt('16:30', '00:30', '0.500000', 720));
    }
}
