<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Billing\Iso8601;
use App\Domain\Exception\BillingException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class Iso8601Test extends TestCase
{
    public function testParsesZuluToUtc(): void
    {
        $value = Iso8601::parseUtc('2026-09-15T11:00:00Z');
        self::assertSame('2026-09-15T11:00:00+00:00', $value->format('Y-m-d\TH:i:sP'));
        self::assertSame('UTC', $value->getTimezone()->getName());
    }

    public function testParsesOffsetByNormalizingToUtc(): void
    {
        $value = Iso8601::parseUtc('2026-09-15T13:00:00+02:00');
        self::assertSame('2026-09-15T11:00:00+00:00', $value->format('Y-m-d\TH:i:sP'));
    }

    public function testParsesMinutePrecisionWithoutSeconds(): void
    {
        $value = Iso8601::parseUtc('2026-09-15T11:00Z');
        self::assertSame('2026-09-15 11:00:00', $value->format('Y-m-d H:i:s'));
    }

    public function testFormatsUtcWithZuluSuffix(): void
    {
        self::assertSame(
            '2026-09-15T11:00:00Z',
            Iso8601::formatUtc(Iso8601::parseUtc('2026-09-15T13:00:00+02:00')),
        );
    }

    /**
     * @throws BillingException
     */
    #[DataProvider('invalidValues')]
    public function testRejectsInvalidDates(string $value): void
    {
        $this->expectException(BillingException::class);
        Iso8601::parseUtc($value);
    }

    public static function invalidValues(): iterable
    {
        yield 'space instead of T' => ['2026-09-15 11:00:00Z'];
        yield 'date only, no timestamp' => ['2026-09-15'];
        yield 'month out of range' => ['2026-99-15T11:00:00Z'];
        yield 'lowercase t' => ['2026-09-15t11:00:00Z'];
    }
}
