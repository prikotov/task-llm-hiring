<?php

declare(strict_types=1);

namespace Tests\Domain\Billing;

use App\Domain\Billing\OffPeakWindow;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function at(string $time): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-09-15T'.$time.':00+00:00');
    }

    public function testSameDayWindowIsHalfOpen(): void
    {
        $w = new OffPeakWindow('09:00', '17:00');
        self::assertFalse($w->contains($this->at('08:59')));
        self::assertTrue($w->contains($this->at('09:00')));
        self::assertTrue($w->contains($this->at('16:59')));
        self::assertFalse($w->contains($this->at('17:00')));
    }

    public function testMidnightWrapWindow(): void
    {
        $w = new OffPeakWindow('16:30', '00:30');
        self::assertTrue($w->contains($this->at('16:30')));
        self::assertTrue($w->contains($this->at('23:59')));
        self::assertTrue($w->contains($this->at('00:00')));
        self::assertTrue($w->contains($this->at('00:29')));
        self::assertFalse($w->contains($this->at('00:30')));
        self::assertFalse($w->contains($this->at('12:00')));
        self::assertFalse($w->contains($this->at('16:29')));
    }

    public function testEqualStartAndEndIsEmptyWindow(): void
    {
        self::assertFalse((new OffPeakWindow('12:00', '12:00'))->contains($this->at('12:00')));
    }
}
