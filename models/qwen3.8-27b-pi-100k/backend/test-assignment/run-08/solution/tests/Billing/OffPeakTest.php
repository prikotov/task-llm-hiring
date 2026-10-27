<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\ValueObject\Decimal;
use App\Billing\ValueObject\OffPeak;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OffPeakTest extends TestCase
{
    private const WRAP = [16 * 60 + 30, 0 * 60 + 30];   // 16:30–00:30, через полночь
    private const PLAIN = [9 * 60, 17 * 60];             // 09:00–17:00, без переноса

    private static function wrap(): OffPeak
    {
        return new OffPeak(Decimal::of('0.5'), self::WRAP[0], self::WRAP[1]);
    }

    private static function plain(): OffPeak
    {
        return new OffPeak(Decimal::of('0.5'), self::PLAIN[0], self::PLAIN[1]);
    }

    /**
     * @return iterable<string, array{string, bool, OffPeak}>
     */
    public static function casesProvider(): iterable
    {
        yield '17:00 — внутри окна' => ['17:00:00', true, self::wrap()];
        yield '23:59:59 — перед полночью' => ['23:59:59', true, self::wrap()];
        yield '00:29:59 — после полуночи (перенос)' => ['00:29:59', true, self::wrap()];
        yield '00:30:00 — конец окна эксклюзивен' => ['00:30:00', false, self::wrap()];
        yield '11:00 — пик' => ['11:00:00', false, self::wrap()];
        yield '16:29:59 — за минуту до начала' => ['16:29:59', false, self::wrap()];
        yield '16:30:00 — начало окна включительно' => ['16:30:00', true, self::wrap()];
        yield '09:00:00 — начало' => ['09:00:00', true, self::plain()];
        yield '16:59:59 — внутри' => ['16:59:59', true, self::plain()];
        yield '17:00:00 — конец эксклюзивен' => ['17:00:00', false, self::plain()];
        yield '08:59:59 — до начала' => ['08:59:59', false, self::plain()];
    }

    #[DataProvider('casesProvider')]
    public function testWindowBoundaries(string $timeOfDay, bool $expected, OffPeak $offPeak): void
    {
        $ts = new DateTimeImmutable('2026-09-10T'.$timeOfDay.'Z', new DateTimeZone('UTC'));
        self::assertSame($expected, $offPeak->isOffPeak($ts));
    }

    public function testMultiplierIsExactDecimal(): void
    {
        self::assertSame('0.500000', self::wrap()->multiplier->toFixed(6));
    }
}
