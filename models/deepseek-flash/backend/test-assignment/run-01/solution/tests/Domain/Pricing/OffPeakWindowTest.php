<?php

declare(strict_types=1);

namespace App\Tests\Domain\Pricing;

use App\Domain\Pricing\OffPeakWindow;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    public function testCrossMidnightWindowIsStartInclusiveAndEndExclusive(): void
    {
        $window = OffPeakWindow::fromStrings('16:30', '00:30', '0.5');

        self::assertTrue($window->contains(new DateTimeImmutable('2026-09-15T16:30:00Z')));
        self::assertTrue($window->contains(new DateTimeImmutable('2026-09-16T00:29:59Z')));
        self::assertFalse($window->contains(new DateTimeImmutable('2026-09-15T16:29:59Z')));
        self::assertFalse($window->contains(new DateTimeImmutable('2026-09-16T00:30:00Z')));
        self::assertFalse($window->contains(new DateTimeImmutable('2026-09-15T12:00:00Z')));
    }

    public function testRegularWindow(): void
    {
        $window = OffPeakWindow::fromStrings('09:00', '17:00', '0.25');

        self::assertTrue($window->contains(new DateTimeImmutable('2026-09-15T09:00:00Z')));
        self::assertTrue($window->contains(new DateTimeImmutable('2026-09-15T16:59:59Z')));
        self::assertFalse($window->contains(new DateTimeImmutable('2026-09-15T17:00:00Z')));
    }

    public function testEqualBoundariesMeanEmptyWindow(): void
    {
        $window = OffPeakWindow::fromStrings('10:00', '10:00', '0.5');

        self::assertFalse($window->contains(new DateTimeImmutable('2026-09-15T10:00:00Z')));
    }

    public function testConvertToUtcBeforeComparing(): void
    {
        $window = OffPeakWindow::fromStrings('16:30', '00:30', '0.5');

        // 20:00+03:00 == 17:00 UTC.
        self::assertTrue($window->contains(new DateTimeImmutable('2026-09-15T20:00:00+03:00')));
    }
}
