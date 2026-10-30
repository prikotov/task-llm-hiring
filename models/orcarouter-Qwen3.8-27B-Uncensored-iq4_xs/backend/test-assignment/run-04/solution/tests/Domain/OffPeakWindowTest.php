<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Catalog\OffPeakWindow;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private static function at(string $hhMm): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-09-15T'.$hhMm.':00+00:00');
    }

    public function testWindowInsideDay(): void
    {
        $window = new OffPeakWindow('01:00', '03:00', '0.5');

        self::assertFalse($window->appliesAtUtc(self::at('00:59')));
        self::assertTrue($window->appliesAtUtc(self::at('01:00')));
        self::assertTrue($window->appliesAtUtc(self::at('02:59')));
        self::assertFalse($window->appliesAtUtc(self::at('03:00')));
    }

    public function testWindowCrossesMidnight(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');

        self::assertFalse($window->appliesAtUtc(self::at('16:29')));
        self::assertTrue($window->appliesAtUtc(self::at('16:30')));
        self::assertTrue($window->appliesAtUtc(self::at('23:59')));
        self::assertTrue($window->appliesAtUtc(self::at('00:00')));
        self::assertTrue($window->appliesAtUtc(self::at('00:29')));
        self::assertFalse($window->appliesAtUtc(self::at('00:30')));
        self::assertFalse($window->appliesAtUtc(self::at('11:00')));
    }

    public function testEmptyWindowWhenBoundsAreEqual(): void
    {
        $window = new OffPeakWindow('12:00', '12:00', '0.5');
        self::assertFalse($window->appliesAtUtc(self::at('12:00')));
        self::assertFalse($window->appliesAtUtc(self::at('00:00')));
    }

    public function testFullDayWhenZeroToMidnight24(): void
    {
        $window = new OffPeakWindow('00:00', '23:59', '0.5');
        self::assertTrue($window->appliesAtUtc(self::at('00:00')));
        self::assertTrue($window->appliesAtUtc(self::at('12:00')));
        self::assertFalse($window->appliesAtUtc(self::at('23:59')));
    }

    public function testInvalidBoundsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OffPeakWindow('24:00', '01:00', '0.5');
    }

    public function testInvalidMultiplierRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OffPeakWindow('01:00', '02:00', '0');
    }
}
