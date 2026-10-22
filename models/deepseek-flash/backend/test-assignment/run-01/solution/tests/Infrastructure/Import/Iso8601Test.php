<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Import;

use App\Infrastructure\Import\Iso8601;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class Iso8601Test extends TestCase
{
    public function testNormalizesOffsetToUtc(): void
    {
        $moment = Iso8601::parse('2026-09-15T14:00:00+03:00', 'ts');

        self::assertSame('2026-09-15T11:00:00+00:00', $moment->format('c'));
    }

    public function testAcceptsDateOnlyAsMidnightUtc(): void
    {
        self::assertSame(
            '2026-09-01T00:00:00+00:00',
            Iso8601::parse('2026-09-01', 'from')->format('c'),
        );
    }

    public function testRejectsValueWithoutTimezone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Iso8601::parse('2026-09-15T11:00:00', 'ts');
    }
}
