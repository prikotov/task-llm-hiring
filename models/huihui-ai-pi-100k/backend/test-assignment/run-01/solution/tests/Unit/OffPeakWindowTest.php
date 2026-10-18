<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\OffPeakWindow;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private static function at(string $hhmm): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-09-15T' . $hhmm . ':00+00:00');
    }

    public static function functionDataProvider(): iterable
    {
        // Окно с переходом через полночь 16:30–00:30 (как в fixtures/prices.json).
        $overnight = OffPeakWindow::create('16:30', '00:30', '0.5');
        yield 'overnight: start inclusive' => [$overnight, '16:30', true];
        yield 'overnight: just before start' => [$overnight, '16:29', false];
        yield 'overnight: evening inside' => [$overnight, '23:59', true];
        yield 'overnight: just after midnight inside' => [$overnight, '00:00', true];
        yield 'overnight: end exclusive' => [$overnight, '00:30', false];
        yield 'overnight: after end outside' => [$overnight, '00:31', false];
        yield 'overnight: noon outside' => [$overnight, '12:00', false];
        yield 'overnight: boundary 15:00 outside' => [$overnight, '15:00', false];

        // Окно в пределах одних суток 09:00–17:00.
        $day = OffPeakWindow::create('09:00', '17:00', '0.7');
        yield 'day: start inclusive' => [$day, '09:00', true];
        yield 'day: before start' => [$day, '08:59', false];
        yield 'day: inside' => [$day, '16:59', true];
        yield 'day: end exclusive' => [$day, '17:00', false];
    }

    #[DataProvider('functionDataProvider')]
    public function testContains(OffPeakWindow $window, string $hhmm, bool $expected): void
    {
        self::assertSame($expected, $window->contains(self::at($hhmm)), "окно в $hhmm");
    }

    public function testDegenerateWindowIsEmpty(): void
    {
        $window = OffPeakWindow::create('05:00', '05:00', '0.5');
        self::assertFalse($window->contains(self::at('05:00')));
        self::assertFalse($window->contains(self::at('10:00')));
    }

    public function testMultiplierAndParts(): void
    {
        $window = OffPeakWindow::create('16:30', '00:30', '0.25');
        self::assertSame('0.25', $window->multiplier());
        self::assertSame(990, $window->startMinutes());
        self::assertSame(30, $window->endMinutes());
    }

    public function testInvalidBoundaries(): void
    {
        foreach (['24:00', '16:75', '1630', '16:5', 'xx:yy', ''] as $bad) {
            $this->expectException(DomainException::class);
            OffPeakWindow::create($bad, '00:30', '0.5');
            $this->expectException(DomainException::class);
            OffPeakWindow::create('16:30', $bad, '0.5');
        }
    }

    public function testInvalidMultiplier(): void
    {
        $this->expectException(DomainException::class);
        OffPeakWindow::create('16:30', '00:30', 'minus');
    }
}
