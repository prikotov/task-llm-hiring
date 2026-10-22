<?php

declare(strict_types=1);

namespace App\Tests\Domain\Pricing;

use App\Domain\Pricing\OffPeakWindow;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private const MULTIPLIER = '0.5';

    private static function window(int $startMin, int $endMin): OffPeakWindow
    {
        return new OffPeakWindow($startMin, $endMin, self::MULTIPLIER);
    }

    #[Test]
    public function dayWindowStartInclusiveEndExclusive(): void
    {
        $window = self::window(16 * 60 + 30, 22 * 60);

        self::assertTrue($window->covers(16 * 60 + 30));
        self::assertTrue($window->covers(19 * 60 + 0));
        self::assertFalse($window->covers(22 * 60));
        self::assertFalse($window->covers(16 * 60 + 29));
        self::assertFalse($window->covers(0));
    }

    #[Test]
    public function windowCrossesMidnight(): void
    {
        $window = self::window(16 * 60 + 30, 0 * 60 + 30);

        self::assertTrue($window->covers(16 * 60 + 30));
        self::assertTrue($window->covers(23 * 60 + 59));
        self::assertTrue($window->covers(0));
        self::assertTrue($window->covers(0 * 60 + 29));
        self::assertFalse($window->covers(0 * 60 + 30));
        self::assertFalse($window->covers(1 * 60));
        self::assertFalse($window->covers(16 * 60 + 29));
    }

    #[Test]
    public function equalBoundariesAreEmptyWindow(): void
    {
        $window = self::window(10 * 60, 10 * 60);

        self::assertFalse($window->covers(10 * 60));
    }

    #[Test]
    public function isOffPeakUsesTimestampMinutes(): void
    {
        $window = self::window(16 * 60 + 30, 0 * 60 + 30);

        self::assertTrue($window->isOffPeak(new \DateTimeImmutable('2026-09-15T17:00:30+00:00')));
        self::assertTrue($window->isOffPeak(new \DateTimeImmutable('2026-09-15T00:29:59+00:00')));
        self::assertFalse($window->isOffPeak(new \DateTimeImmutable('2026-09-15T00:30:00+00:00')));
        self::assertFalse($window->isOffPeak(new \DateTimeImmutable('2026-09-15T09:00:00+00:00')));
    }

    #[Test]
    public function multiplierIsExposedAsDecimalString(): void
    {
        self::assertSame('0.5', self::window(0, 60)->multiplier());
    }
}
