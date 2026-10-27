<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\UtcDateTime;
use App\Exception\ImportException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UtcDateTimeTest extends TestCase
{
    public function test_roundtrip(): void
    {
        $ts = UtcDateTime::parse('2026-09-15T12:00:00Z', 'ts');
        self::assertSame('2026-09-15T12:00:00Z', $ts->format('Y-m-d\TH:i:s\Z'));
    }

    public function test_fractional_seconds(): void
    {
        $ts = UtcDateTime::parse('2026-09-15T12:00:00.987Z', 'ts');
        self::assertSame('2026-09-15 12:00:00', $ts->format('Y-m-d H:i:s'));
    }

    #[DataProvider('badProvider')]
    public function test_rejects_non_utc_and_garbage(string $value): void
    {
        $this->expectException(ImportException::class);
        UtcDateTime::parse($value, 'ts');
    }

    public static function badProvider(): array
    {
        return [
            'смещение +02:00'         => ['2026-09-15T12:00:00+02:00'],
            'пространство вместо T'   => ['2026-09-15 12:00:00Z'],
            'без времени'             => ['2026-09-15Z'],
            'мусор'                   => ['vfrq'],
            'несуществующий день'     => ['2026-02-30T12:00:00Z'],
        ];
    }
}
