<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Util\DateTimeUtc;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DateTimeUtcTest extends TestCase
{
    public function testParsesZSuffix(): void
    {
        $dt = DateTimeUtc::parse('2026-09-15T11:00:00Z');
        self::assertSame('2026-09-15 11:00:00', $dt->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $dt->getTimezone()->getName());
    }

    public function testNormalizesOffsetsToUtc(): void
    {
        self::assertSame('11:00:00', DateTimeUtc::parse('2026-09-15T14:00:00+03:00')->format('H:i:s'));
        self::assertSame('11:00:00', DateTimeUtc::parse('2026-09-15T05:00:00-06:00')->format('H:i:s'));
    }

    public function testWithoutSeconds(): void
    {
        $dt = DateTimeUtc::parse('2026-09-15T11:00Z');
        self::assertSame('2026-09-15 11:00:00', $dt->format('Y-m-d H:i:s'));
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidInputRejected(string $value): void
    {
        $this->expectException(DomainException::class);
        DateTimeUtc::parse($value);
    }

    public static function invalidProvider(): iterable
    {
        yield 'not a date' => ['not-a-date'];
        yield 'month overflow' => ['2026-13-40T99:99:00Z'];
        yield 'empty' => [''];
        yield 'fraction without digits' => ['2026-09-15T11:00:00.'];
    }

    public function testFormatIsIso8601Utc(): void
    {
        $dt = DateTimeUtc::parse('2026-09-15T14:00:00+03:00');
        self::assertSame('2026-09-15T11:00:00Z', DateTimeUtc::format($dt));
    }
}
