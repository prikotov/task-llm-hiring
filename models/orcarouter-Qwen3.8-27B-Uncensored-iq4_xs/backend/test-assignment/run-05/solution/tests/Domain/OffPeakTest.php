<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\ValueObject\OffPeak;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private static function window(?array $source): ?OffPeak
    {
        return OffPeak::create($source);
    }

    private static function ts(string $time): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-09-15T'.$time.':00 UTC');
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: bool}>
     */
    public static function boundariesProvider(): array
    {
        $window = ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0.5'];

        return [
            'начало окна включительно' => [$window, '16:30', true],
            'окончание окна исключительно' => [$window, '00:30', false],
            'сразу после начала' => [$window, '16:31', true],
            'сразу до начала' => [$window, '16:29', false],
            'сразу после окончания' => [$window, '00:31', false],
            'сразу до окончания (через полночь)' => [$window, '00:29', true],
            'ночь вне загибов окна (03:00 — после 00:30)' => [$window, '03:00', false],
            'полдень вне окна' => [$window, '12:00', false],
            'конец суток — внутри окна через полночь' => [$window, '23:59', true],
            'начало суток — внутри окна через полночь' => [$window, '00:00', true],
        ];
    }

    #[DataProvider('boundariesProvider')]
    public function testWindowBounds(array $window, string $time, bool $expected): void
    {
        self::assertSame($expected, self::window($window)->covers(self::ts($time)));
    }

    public function testNonMidnightWindow(): void
    {
        $window = self::window(['window_start' => '02:00', 'window_end' => '04:00', 'multiplier' => '0.7']);

        self::assertTrue($window->covers(self::ts('03:59')));
        self::assertFalse($window->covers(self::ts('04:00')));
        self::assertFalse($window->covers(self::ts('01:59')));
    }

    public function testDegenerateWindowIsEmpty(): void
    {
        $window = self::window(['window_start' => '05:00', 'window_end' => '05:00', 'multiplier' => '0.5']);

        self::assertFalse($window->covers(self::ts('05:00')));
        self::assertFalse($window->covers(self::ts('15:00')));
    }

    public function testNullSourceMeansNoDiscount(): void
    {
        self::assertNull(self::window(null));
    }
}
