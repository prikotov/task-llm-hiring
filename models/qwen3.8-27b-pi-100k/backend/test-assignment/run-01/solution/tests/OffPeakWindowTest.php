<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\OffPeakWindow;
use PHPUnit\Framework\TestCase;
use ValueError;

final class OffPeakWindowTest extends TestCase
{
    /** Окно 16:30–00:30 UTC (через полночь). */
    private function window(): OffPeakWindow
    {
        return OffPeakWindow::create(990, 30, '0.5');
    }

    public function testBoundaries(): void
    {
        $w = $this->window();

        self::assertTrue($w->containsMinute(990), '16:30 — начало окна включительно');
        self::assertTrue($w->containsMinute(1020), '17:00 — внутри');
        self::assertTrue($w->containsMinute(1439), '23:59 — внутри (через полночь)');
        self::assertTrue($w->containsMinute(0), '00:00 — внутри');
        self::assertTrue($w->containsMinute(29), '00:29 — внутри');
        self::assertFalse($w->containsMinute(30), '00:30 — конец окна эксклюзивно');
        self::assertFalse($w->containsMinute(989), '16:29 — до окна');
    }

    public function testSimpleWindow(): void
    {
        $w = OffPeakWindow::create(120, 180, '0.8');

        self::assertTrue($w->containsMinute(120));
        self::assertTrue($w->containsMinute(150));
        self::assertFalse($w->containsMinute(180));
        self::assertFalse($w->containsMinute(119));
    }

    public function testEmptyWindowHasNoEffect(): void
    {
        $w = OffPeakWindow::create(120, 120, '0.8');

        self::assertFalse($w->containsMinute(120));
        self::assertFalse($w->containsMinute(60));
    }

    public function testOutOfRangeWindowIsRejected(): void
    {
        $this->expectException(ValueError::class);
        OffPeakWindow::create(1500, 300, '0.5');
    }

    public function testNegativeMultiplierIsRejected(): void
    {
        $this->expectException(ValueError::class);
        OffPeakWindow::create(120, 180, '-0.5');
    }
}
