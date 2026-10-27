<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\OffPeak;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Окно оф-пика UTC: [start, end) включительно/исключительно, переход
 * через полночь, нет скидки при start == end.
 */
final class OffPeakTest extends TestCase
{
    private const WINDOW = ['16:30', '00:30', '0.5'];

    public static function providesApplies(): iterable
    {
        // [время UTC, в окне?]
        yield 'start включительно' => ['16:30:00', true];
        yield 'минута до start' => ['16:29:59', false];
        yield 'глубоко внутри' => ['20:00:00', true];
        yield 'после полуночи в окне' => ['00:00:00', true];
        yield 'до конца окна' => ['00:29:59', true];
        yield 'end исключительно' => ['00:30:00', false];
        yield 'день, вне окна' => ['09:15:00', false];
        yield 'ровно до полночи' => ['23:59:59', true];
        yield 'минута после полудня до окна' => ['15:59:00', false];
        yield '00:31 вне окна' => ['00:31:00', false];
    }

    #[DataProvider('providesApplies')]
    public function testWindowWithMidnightCrossing(string $timeIso, bool $expected): void
    {
        $offPeak = self::make();
        $ts = new \DateTimeImmutable('2026-09-15T' . $timeIso . 'Z', new \DateTimeZone('UTC'));
        self::assertSame($expected, $offPeak->appliesAtUtc($ts));
    }

    public function testWindowWithoutCrossing(): void
    {
        $offPeak = self::make(['08:00', '17:00', '0.7']);
        $utc = new \DateTimeZone('UTC');
        $in = new \DateTimeImmutable('2026-09-15T12:00:00Z', $utc);
        $startInclusive = new \DateTimeImmutable('2026-09-15T08:00:00Z', $utc);
        $endExclusive = new \DateTimeImmutable('2026-09-15T17:00:00Z', $utc);
        $before = new \DateTimeImmutable('2026-09-15T07:59:59Z', $utc);

        self::assertTrue($offPeak->appliesAtUtc($startInclusive));
        self::assertTrue($offPeak->appliesAtUtc($in));
        self::assertFalse($offPeak->appliesAtUtc($endExclusive));
        self::assertFalse($offPeak->appliesAtUtc($before));
    }

    public function testEmptyWindowAppliesNowhere(): void
    {
        $offPeak = self::make(['10:00', '10:00', '0.5']);
        $ts = new \DateTimeImmutable('2026-09-15T10:00:00Z', new \DateTimeZone('UTC'));
        self::assertFalse($offPeak->appliesAtUtc($ts));
    }

    public function testInvalidTimeThrows(): void
    {
        $this->expectException(\ValueError::class);
        self::make(['25:00', '01:00', '0.5']);
    }

    public function testFromProviderNullMeansNoDiscount(): void
    {
        self::assertNull(OffPeak::fromProvider(null, null, null));
        self::assertNull(OffPeak::fromProvider('16:30', null, '0.5'));

        $offPeak = OffPeak::fromProvider('16:30', '00:30', '0.5');
        self::assertNotNull($offPeak);
        self::assertSame('0.5', $offPeak->multiplier());
    }

    /**
     * @param array{0: string, 1: string, 2: string} $window
     */
    private static function make(array $window): OffPeak
    {
        $offPeak = OffPeak::fromProvider($window[0], $window[1], $window[2]);
        self::assertNotNull($offPeak);

        return $offPeak;
    }
}
