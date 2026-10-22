<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeak;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private function at(string $hms): DateTimeImmutable
    {
        return new DateTimeImmutable($hms, new DateTimeZone('UTC'));
    }

    public function testWindowCrossingMidnight(): void
    {
        $off = new OffPeak('16:30', '00:30', '0.5');

        self::assertTrue($off->contains($this->at('16:30:00')), 'start включительно');
        self::assertTrue($off->contains($this->at('17:00:00')));
        self::assertTrue($off->contains($this->at('23:59:59')));
        self::assertTrue($off->contains($this->at('00:29:59')));
        self::assertFalse($off->contains($this->at('00:30:00')), 'end эксклюзивно');
        self::assertFalse($off->contains($this->at('16:29:59')));
        self::assertFalse($off->contains($this->at('12:00:00')));
    }

    public function testWindowSameDay(): void
    {
        $off = new OffPeak('09:00', '17:00', '0.7');

        self::assertTrue($off->contains($this->at('09:00:00')));
        self::assertTrue($off->contains($this->at('16:59:59')));
        self::assertFalse($off->contains($this->at('17:00:00')));
        self::assertFalse($off->contains($this->at('08:59:59')));
    }
}
