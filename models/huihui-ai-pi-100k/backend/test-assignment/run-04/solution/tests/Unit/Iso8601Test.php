<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Support\Iso8601;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class Iso8601Test extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidValues(string $value, string $expectedUtc): void
    {
        $dt = Iso8601::parseUtc($value);
        self::assertSame($expectedUtc, $dt->format('Y-m-d\TH:i:s'));
        self::assertSame('+00:00', $dt->format('P'));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function validProvider(): array
    {
        return [
            'zulu' => ['2026-09-15T11:00:00Z', '2026-09-15T11:00:00'],
            'offset_plus' => ['2026-09-15T16:30:00+05:30', '2026-09-15T11:00:00'],
            'offset_minus' => ['2026-09-15T03:00:00-08:00', '2026-09-15T11:00:00'],
            'naive_treated_as_utc' => ['2026-09-15T11:00:00', '2026-09-15T11:00:00'],
            'naive_space' => ['2026-09-15 11:00:00', '2026-09-15T11:00:00'],
            'date_seconds_only' => ['2026-09-15T11:00Z', '2026-09-15T11:00:00'],
            'sub_seconds_truncated' => ['2026-09-15T11:00:00.588Z', '2026-09-15T11:00:00'],
            'leap_second_like_59' => ['2026-09-15T11:00:59Z', '2026-09-15T11:00:59'],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidValues(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Iso8601::parseUtc($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidProvider(): array
    {
        return [
            'empty' => [''],
            'date_only' => ['2026-09-15'],
            'bad_month' => ['2026-13-01T00:00:00Z'],
            'bad_day' => ['2026-09-32T00:00:00Z'],
            'hour_24' => ['2026-09-15T24:00:00Z'],
            'minute_60' => ['2026-09-15T11:60:00Z'],
            'garbage' => ['not-a-date'],
            'trailing_junk' => ['2026-09-15T11:00:00Z extra'],
            'offset_without_colon' => ['2026-09-15T11:00:00+0530'],
        ];
    }
}
