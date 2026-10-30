<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Support\IsoTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IsoTimeTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testParsesUtcValues(string $value, string $expected): void
    {
        $dt = IsoTime::parseUtc($value, 'test');
        self::assertSame($expected, $dt->format('Y-m-d H:i:s'));
        self::assertContains($dt->getTimezone()->getName(), ['UTC', 'Z']);
    }

    public static function validValues(): \Generator
    {
        yield 'символ Z' => ['2026-08-01T00:00:00Z', '2026-08-01 00:00:00'];
        yield 'с дробными секундами' => ['2026-08-01T12:34:56.123456Z', '2026-08-01 12:34:56'];
    }

    public function testRejectsNonUtcAndBadDates(): void
    {
        $rejected = 0;
        foreach (['2026-08-01T00:00:00+02:00', '2026-02-30T10:00:00Z', '2026-08-01 10:00:00Z', 'now', '2026-08-01T24:00:00Z'] as $bad) {
            try {
                IsoTime::parseUtc($bad, 'test');
                self::fail(
                    sprintf('"%s" не отклонён', $bad),
                );
            } catch (DomainException) {
                ++$rejected;
            }
        }

        self::assertSame(5, $rejected);
    }
}
