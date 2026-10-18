<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Pricing\OffPeakWindow;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function at(string $hm): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-01-01T'.$hm.':00+00:00', new DateTimeZone('UTC'));
    }

    public function testWindowCrossingMidnight(): void
    {
        $window = new OffPeakWindow(990, 30, '0.5'); // 16:30–00:30

        self::assertFalse($window->isWithin($this->at('16:29')));
        self::assertTrue($window->isWithin($this->at('16:30')));
        self::assertTrue($window->isWithin($this->at('23:59')));
        self::assertTrue($window->isWithin($this->at('00:00')));
        self::assertTrue($window->isWithin($this->at('00:29')));
        self::assertFalse($window->isWithin($this->at('00:30')));
        self::assertFalse($window->isWithin($this->at('10:00')));
    }

    public function testDayWindow(): void
    {
        $window = new OffPeakWindow(540, 1080, '1'); // 09:00–18:00

        self::assertFalse($window->isWithin($this->at('08:59')));
        self::assertTrue($window->isWithin($this->at('09:00')));
        self::assertTrue($window->isWithin($this->at('17:59')));
        self::assertFalse($window->isWithin($this->at('18:00')));
    }

    public function testDegenerateWindowIsNotContaining(): void
    {
        $window = new OffPeakWindow(600, 600, '0.5'); // пусто

        self::assertFalse($window->isWithin($this->at('10:00')));
        self::assertFalse($window->isWithin($this->at('10:01')));
        self::assertFalse($window->isWithin($this->at('23:59')));
    }

    public function testMultipliedValueIsStored(): void
    {
        $window = new OffPeakWindow(990, 30, '0.5');
        self::assertSame('0.5', $window->multiplier());
    }
}
