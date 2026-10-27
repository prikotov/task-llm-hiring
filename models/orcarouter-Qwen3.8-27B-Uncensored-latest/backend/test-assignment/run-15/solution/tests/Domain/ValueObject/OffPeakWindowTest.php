<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use App\Domain\ValueObject\OffPeakWindow;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private OffPeakWindow $window; // 16:30–00:30 (crosses midnight)

    protected function setUp(): void
    {
        $this->window = OffPeakWindow::fromPayload('16:30', '00:30', '0.5');
    }

    private function ts(string $t): \DateTimeImmutable
    {
        return new \DateTimeImmutable($t, new \DateTimeZone('UTC'));
    }

    public function test_windowStartInclusive(): void
    {
        self::assertTrue($this->window->containsUtc($this->ts('2026-09-15T16:30:00Z')));
    }

    public function test_windowEndExclusive(): void
    {
        self::assertFalse($this->window->containsUtc($this->ts('2026-09-16T00:30:00Z')));
    }

    public function test_lastSecondBeforeMidnightInsideWindow(): void
    {
        self::assertTrue($this->window->containsUtc($this->ts('2026-09-15T23:59:59Z')));
    }

    public function test_fewSecondsAfterMidnightInsideWindow(): void
    {
        self::assertTrue($this->window->containsUtc($this->ts('2026-09-16T00:15:00Z')));
    }

    public function test_peakHoursOutsideWindow(): void
    {
        self::assertFalse($this->window->containsUtc($this->ts('2026-09-15T11:00:00Z')));
        self::assertFalse($this->window->containsUtc($this->ts('2026-09-15T12:00:00Z')));
        self::assertFalse($this->window->containsUtc($this->ts('2026-09-15T06:00:00Z')));
    }

    public function test_justBeforeWindowStart(): void
    {
        self::assertFalse($this->window->containsUtc($this->ts('2026-09-15T16:29:59Z')));
    }

    public function test_multiplier(): void
    {
        self::assertSame('0.5', $this->window->multiplier());
    }

    public function test_nonCrossingWindow(): void
    {
        // Window 09:00–17:00 (does not cross midnight)
        $w = OffPeakWindow::fromPayload('09:00', '17:00', '0.8');
        self::assertTrue($w->containsUtc($this->ts('2026-09-15T09:00:00Z')));
        self::assertTrue($w->containsUtc($this->ts('2026-09-15T16:59:59Z')));
        self::assertFalse($w->containsUtc($this->ts('2026-09-15T17:00:00Z')));
        self::assertFalse($w->containsUtc($this->ts('2026-09-15T08:59:59Z')));
        self::assertSame('0.8', $w->multiplier());
    }
}
