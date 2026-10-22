<?php

declare(strict_types=1);

namespace App\Tests\Pricing;

use App\Domain\Pricing\OffPeakWindow;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    private static function utc(string $time): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-09-15 ' . $time, new DateTimeZone('UTC'));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function wrappingWindow(): iterable
    {
        yield 'до начала' => ['16:29', false];
        yield 'начало включительно' => ['16:30', true];
        yield 'внутри вечера' => ['18:00', true];
        yield 'перед полуночью' => ['23:59', true];
        yield 'полночь' => ['00:00', true];
        yield 'конец исключительно' => ['00:30', false];
        yield 'утро' => ['09:00', false];
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function sameDayWindow(): iterable
    {
        yield 'до' => ['09:59', false];
        yield 'начало' => ['10:00', true];
        yield 'внутри' => ['12:00', true];
        yield 'конец исключительно' => ['13:00', false];
    }

    #[DataProvider('wrappingWindow')]
    public function testWindowWrappingMidnight(string $time, bool $expected): void
    {
        $window = OffPeakWindow::fromStrings('16:30', '00:30', '0.5');

        self::assertSame($expected, $window->contains(self::utc($time)));
    }

    #[DataProvider('sameDayWindow')]
    public function testWindowWithinOneDay(string $time, bool $expected): void
    {
        $window = OffPeakWindow::fromStrings('10:00', '13:00', '0.5');

        self::assertSame($expected, $window->contains(self::utc($time)));
    }

    public function testEmptyWindowNeverApplies(): void
    {
        $window = OffPeakWindow::fromStrings('10:00', '10:00', '0.5');

        self::assertFalse($window->contains(self::utc('10:00')));
    }

    public function testMultiplierIsExactDecimal(): void
    {
        $window = OffPeakWindow::fromStrings('16:30', '00:30', '0.5');

        self::assertSame('0.500000', $window->multiplier->fixed(6));
    }
}
