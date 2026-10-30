<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Pricing\ValueObject\OffPeakWindow;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakWindowTest extends TestCase
{
    #[DataProvider('crossMidnightProvider')]
    public function testWindowCrossingMidnight(string $time, bool $expected): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');
        $moment = new DateTimeImmutable('2026-09-15T'.$time.'Z', new DateTimeZone('UTC'));

        self::assertSame($expected, $window->contains($moment));
    }

    /** @return iterable<string, array{string, bool}> */
    public static function crossMidnightProvider(): iterable
    {
        yield 'before start' => ['16:29:59', false];
        yield 'at start' => ['16:30:00', true];
        yield 'evening' => ['20:00:00', true];
        yield 'midnight' => ['00:00:00', true];
        yield 'before end' => ['00:29:59', true];
        yield 'at end' => ['00:30:00', false];
    }

    #[DataProvider('sameDayProvider')]
    public function testWindowWithinDay(string $time, bool $expected): void
    {
        $window = new OffPeakWindow('08:00', '17:00', '0.75');
        $moment = new DateTimeImmutable('2026-09-15T'.$time.'Z', new DateTimeZone('UTC'));

        self::assertSame($expected, $window->contains($moment));
    }

    /** @return iterable<string, array{string, bool}> */
    public static function sameDayProvider(): iterable
    {
        yield 'before' => ['07:59:59', false];
        yield 'start' => ['08:00:00', true];
        yield 'end' => ['17:00:00', false];
    }

    public function testUtcIsUsedForComparison(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');
        $moment = new DateTimeImmutable('2026-09-15T20:00:00+03:00');

        self::assertTrue($window->contains($moment));
    }
}
