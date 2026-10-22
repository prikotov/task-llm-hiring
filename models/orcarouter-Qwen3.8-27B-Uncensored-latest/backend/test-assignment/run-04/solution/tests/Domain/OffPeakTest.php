<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Pricing\OffPeak;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private OffPeak $window;

    protected function setUp(): void
    {
        // 16:30 (inclusive) .. 00:30 (exclusive), crossing midnight
        $this->window = new OffPeak('16:30', '00:30', '0.5');
    }

    #[DataProvider('timeProvider')]
    public function testWindowBoundaries(string $time, bool $expected): void
    {
        $dt = new \DateTimeImmutable($time, new \DateTimeZone('UTC'));

        self::assertSame($expected, $this->window->contains($dt), \sprintf('%s (expected %d)', $time, (int) $expected));
    }

    public static function timeProvider(): iterable
    {
        yield ['16:29:59', false];
        yield ['16:30:00', true]; // start inclusive
        yield ['23:59:59', true];
        yield ['00:00:00', true];
        yield ['00:29:59', true];
        yield ['00:30:00', false]; // end exclusive
        yield ['10:00:00', false];
    }

    #[DataProvider('dayWindowProvider')]
    public function testDayWindow(string $start, string $end, string $time, bool $expected): void
    {
        $window = new OffPeak($start, $end, '0.75');
        $dt = new \DateTimeImmutable($time, new \DateTimeZone('UTC'));

        self::assertSame($expected, $window->contains($dt));
    }

    public static function dayWindowProvider(): iterable
    {
        // 02:00 .. 10:00 (no midnight crossing)
        yield ['02:00', '10:00', '01:59:59', false];
        yield ['02:00', '10:00', '02:00:00', true];
        yield ['02:00', '10:00', '09:59:59', true];
        yield ['02:00', '10:00', '10:00:00', false];
    }

    public function testApplyMultiplier(): void
    {
        self::assertSame('0.5000000000000000', $this->window->apply('1.0000000000000000'));
        self::assertSame('7.1312493750000000', $this->window->apply('14.26249875'));
    }

    public function testInvalidWindows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OffPeak('25:99', '00:30', '0.5');
    }

    public function testMultiplierMustBePositive(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OffPeak('16:30', '00:30', '0');
    }
}
