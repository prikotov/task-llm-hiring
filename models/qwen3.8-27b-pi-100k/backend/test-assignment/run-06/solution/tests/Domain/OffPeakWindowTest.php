<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Billing\OffPeakWindow;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function at(string $iso): \DateTimeImmutable
    {
        return new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
    }

    public function testWindowSpanningMidnight(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');

        self::assertTrue($window->contains($this->at('2026-09-01T16:30:00Z')));
        self::assertTrue($window->contains($this->at('2026-09-01T23:59:00Z')));
        self::assertTrue($window->contains($this->at('2026-09-02T00:29:00Z')));
        self::assertFalse($window->contains($this->at('2026-09-02T00:30:00Z')));
        self::assertFalse($window->contains($this->at('2026-09-01T12:00:00Z')));
    }

    public function testSimpleWindow(): void
    {
        $window = new OffPeakWindow('01:00', '02:00', '0.25');

        self::assertTrue($window->contains($this->at('2026-09-01T01:30:00Z')));
        self::assertFalse($window->contains($this->at('2026-09-01T02:00:00Z')));
        self::assertFalse($window->contains($this->at('2026-09-01T00:59:00Z')));
    }

    public function testValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new OffPeakWindow('25:00', '01:00', '0.5');
    }

    public function testMultiplierValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new OffPeakWindow('01:00', '02:00', '-0.5');
    }
}
