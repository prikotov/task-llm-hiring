<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Billing\OffPeakWindow;
use App\Entity\Provider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class OffPeakWindowTest extends TestCase
{
    public function testFromCodesParsesMinutes(): void
    {
        $w = OffPeakWindow::fromCodes('16:30', '00:30', '0.5');
        self::assertSame(990, $w->startMinutes);
        self::assertSame(30, $w->endMinutes);
        self::assertSame('0.5', $w->multiplier);
    }

    public function testParseMinutes(): void
    {
        self::assertSame(990, OffPeakWindow::parseMinutes('16:30'));
        self::assertSame(5, OffPeakWindow::parseMinutes('00:05'));
        self::assertSame(0, OffPeakWindow::parseMinutes('00:00'));
    }

    public function testCoversWindowCrossingMidnight(): void
    {
        $w = OffPeakWindow::fromCodes('16:30', '00:30', '0.5');
        $t = fn (string $hmi): DateTimeImmutable => new DateTimeImmutable('2026-09-15T'.substr($hmi, 0, 5).':00Z');

        self::assertTrue($w->covers($t('18:15')));  // внутри окна
        self::assertTrue($w->covers($t('16:30')));  // начало включительно
        self::assertFalse($w->covers($t('16:29'))); // до начала
        self::assertFalse($w->covers($t('10:00'))); // вне окна
        self::assertTrue($w->covers($t('23:59')));  // перед полуночью
        self::assertTrue($w->covers($t('00:00')));  // после полуночи
        self::assertTrue($w->covers($t('00:29')));  // до конца
        self::assertFalse($w->covers($t('00:30'))); // конец исключительно
    }

    public function testFromProviderReturnsNullWhenNoOffPeak(): void
    {
        $provider = new Provider();
        $provider->setCode('sfera');
        self::assertNull(OffPeakWindow::fromProvider($provider));
    }

    public function testFromProviderReadsProviderWindow(): void
    {
        $provider = new Provider();
        $provider->setCode('prism');
        $provider->setOffPeak('16:30', '00:30', '0.5');

        $w = OffPeakWindow::fromProvider($provider);
        self::assertNotNull($w);
        self::assertSame(990, $w->startMinutes);
        self::assertSame(30, $w->endMinutes);
    }
}
