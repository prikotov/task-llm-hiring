<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Domain\OffPeakWindow;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    public function testDaytimeWindowInclusiveStartExclusiveEnd(): void
    {
        $w = OffPeakWindow::parse('16:30', '00:30');

        self::assertTrue($w->contains('16:30'));   // start включительно
        self::assertTrue($w->contains('17:10'));
        self::assertTrue($w->contains('00:00'));  // окно переходит через полночь
        self::assertFalse($w->contains('00:30')); // end exclusively
        self::assertFalse($w->contains('11:00')); // дневной пик
        self::assertFalse($w->contains('16:29'));
    }

    public function testPlainDaytimeWindow(): void
    {
        $w = OffPeakWindow::parse('01:00', '05:00');

        self::assertTrue($w->contains('01:00'));
        self::assertFalse($w->contains('05:00'));
        self::assertFalse($w->contains('23:59'));
    }

    public function testRejectsBadTimeFormat(): void
    {
        $this->expectException(DomainException::class);
        OffPeakWindow::parse('25:00', '07:00');
    }
}
