<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Billing\OffPeak;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    public function testSimpleWindowExclusiveEnd(): void
    {
        // Окно 09:00 -> 17:00, не пересекающее полночь.
        $win = OffPeak::fromWindowAndMultiplier('09:00', '17:00', '0.5');
        self::assertFalse($win->appliesAt(8 * 60 + 59));  // 08:59 — до начала
        self::assertTrue($win->appliesAt(9 * 60));        // 09:00 — включительно
        self::assertTrue($win->appliesAt(16 * 60 + 59));  // 16:59
        self::assertFalse($win->appliesAt(17 * 60));      // 17:00 — исключительно
    }

    public function testCrossingMidnightWindow(): void
    {
        // Фикстурное окно prism: 16:30 -> 00:30 (через полночь).
        $win = OffPeak::fromWindowAndMultiplier('16:30', '00:30', '0.5');
        self::assertFalse($win->appliesAt(16 * 60 + 29)); // 16:29 — до начала
        self::assertTrue($win->appliesAt(16 * 60 + 30));  // 16:30 — включительно
        self::assertTrue($win->appliesAt(23 * 60));       // 23:00
        self::assertTrue($win->appliesAt(15));            // 00:15
        self::assertFalse($win->appliesAt(30));           // 00:30 — исключительно
        self::assertFalse($win->appliesAt(9 * 60));       // 09:00
    }

    public function testEmptyWindow(): void
    {
        $win = OffPeak::fromWindowAndMultiplier('16:30', '16:30', '0.5');
        self::assertFalse($win->appliesAt(16 * 60 + 30));
        self::assertFalse($win->appliesAt(0));
    }
}
