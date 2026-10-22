<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Billing\OffPeakPolicy;
use App\Domain\Shared\TimeOfDay;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakPolicyTest extends TestCase
{
    private const WINDOW = 990;   // 16:30
    private const END = 30;       // 00:30

    #[DataProvider('provideWindowCases')]
    public function testWindow(int $minute, bool $expected): void
    {
        $policy = new OffPeakPolicy(self::WINDOW, self::END, '0.5');

        self::assertSame($expected, $policy->applies($minute));
    }

    public static function provideWindowCases(): iterable
    {
        yield 'at window start (inclusive)' => [990, true];
        yield 'inside window' => [1080, true];   // 18:00
        yield 'end-1 minute (inclusive up to)' => [1439, true]; // 23:59
        yield 'after midnight before end' => [15, true];        // 00:15
        yield 'at window end (exclusive)' => [30, false];       // 00:30
        yield 'between end and start' => [45, false];           // 00:45
        yield 'one minute before start' => [989, false];        // 16:29
    }

    public function testMultiplierIsExposed(): void
    {
        self::assertSame('0.5', (new OffPeakPolicy(990, 30, '0.5'))->multiplier());
    }

    public function testTimeOfDayMinutes(): void
    {
        self::assertSame(990, TimeOfDay::minutesOfDayUtc(new \DateTimeImmutable('2026-09-14T16:30:00Z')));
        self::assertSame(30, TimeOfDay::minutesOfDayUtc(new \DateTimeImmutable('2026-09-14T00:30:00Z')));
        self::assertSame(0, TimeOfDay::minutesOfDayUtc(new \DateTimeImmutable('2026-09-14T00:00:00Z')));
        self::assertSame(23 * 60 + 59, TimeOfDay::minutesOfDayUtc(new \DateTimeImmutable('2026-09-14T23:59Z')));
        self::assertSame(30, TimeOfDay::minutesOfDayUtc(new \DateTimeImmutable('2026-09-14T00:30Z')));
        // Не-UTC представление приводится к минутам UTC.
        self::assertSame(870, TimeOfDay::minutesOfDayUtc(new \DateTimeImmutable('2026-09-14T16:30:00+02:00')));
    }

    #[DataProvider("provideBadHhmm")]
    public function testHhmmRejection(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TimeOfDay::parseHhmm($value, 'test');
    }

    public static function provideBadHhmm(): iterable
    {
        yield 'hour overflow' => ['24:00'];
        yield 'minute overflow' => ['16:70'];
        yield 'short minute' => ['16:7'];
        yield 'seconds' => ['16:30:00'];
        yield 'garbage' => ['x:y'];
        yield 'leading zero double digit' => ['016:00'];
    }
}
