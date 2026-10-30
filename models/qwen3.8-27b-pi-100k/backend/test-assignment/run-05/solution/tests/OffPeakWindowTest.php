<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\OffPeakWindow;
use App\Entity\Provider;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private function at(string $time): \DateTimeImmutable
    {
        $timezone = new DateTimeZone('UTC');

        return new DateTimeImmutable($time, $timezone);
    }

    public function testWindowCrossingMidnight(): void
    {
        $provider = new Provider('prism', 'Prism');
        $provider->setOffPeak('16:30', '00:30', '0.5');

        // Внутри окна
        self::assertTrue(OffPeakWindow::contains($provider, $this->at('2026-09-15T16:30:00Z')));
        self::assertTrue(OffPeakWindow::contains($provider, $this->at('2026-09-15T23:59:00Z')));
        self::assertTrue(OffPeakWindow::contains($provider, $this->at('2026-09-15T00:00:00Z')));
        self::assertTrue(OffPeakWindow::contains($provider, $this->at('2026-09-15T00:29:59Z')));
        // Вне окна: нижняя граница включительно, верхняя — исключительно
        self::assertFalse(OffPeakWindow::contains($provider, $this->at('2026-09-15T16:29:59Z')));
        self::assertFalse(OffPeakWindow::contains($provider, $this->at('2026-09-15T00:30:00Z')));
        self::assertFalse(OffPeakWindow::contains($provider, $this->at('2026-09-15T11:00:00Z')));
    }

    public function testWindowNotCrossingMidnight(): void
    {
        $provider = new Provider('sfera', 'Sfera');
        $provider->setOffPeak('02:00', '04:00', '0.7');

        self::assertTrue(OffPeakWindow::contains($provider, $this->at('2026-09-15T02:00:00Z')));
        self::assertTrue(OffPeakWindow::contains($provider, $this->at('2026-09-15T03:59:00Z')));
        self::assertFalse(OffPeakWindow::contains($provider, $this->at('2026-09-15T04:00:00Z')));
        self::assertFalse(OffPeakWindow::contains($provider, $this->at('2026-09-16T01:59:00Z')));
    }

    public function testNoOffPeakMeansPeak(): void
    {
        $provider = new Provider('sfera', 'Sfera');
        $provider->setOffPeak(null, null, null);

        self::assertFalse(OffPeakWindow::contains($provider, $this->at('2026-09-15T03:00:00Z')));
    }
}
