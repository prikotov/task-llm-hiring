<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\OffPeakPolicy;
use App\Domain\Rater;
use App\Domain\UnitRates;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

final class RaterTest extends TestCase
{
    private static function tokens(float $in = 0, float $out = 0, float $cr = 0, float $cw = 0): array
    {
        return [
            'input' => (int) $in,
            'output' => (int) $out,
            'cache_read' => (int) $cr,
            'cache_write' => (int) $cw,
        ];
    }

    public function testPerTokenSchemeMatchesTaskExample(): void
    {
        $rates = new UnitRates(
            unit: Rater::UNIT_PER_TOKEN,
            input: '2.50',
            output: '10.00',
            cacheRead: '2.50',
            cacheWrite: '3.125',
        );

        // 1 200 000 input * 2.50 + 800 000 output * 10.00 + 2 500 000 * 2.5 + 83 333 * 3.125
        // = 3 000 000 + 8 000 000 + 6 250 000 + 260 415.625 = 17 510 415.625 / 1 000 000
        self::assertSame('17.510416', Rater::cost(self::tokens(1200000, 800000, 2500000, 83333), $rates, null, new DateTimeImmutable()));
    }

    public function testUnifiedScheme(): void
    {
        $rates = new UnitRates(unit: Rater::UNIT_UNIFIED, any: '5.00');

        // (3 000 000 + 500 000) * 5.00 / 1 000 000 = 17.5
        self::assertSame('17.500000', Rater::cost(self::tokens(3000000, 500000), $rates, null, new DateTimeImmutable()));
    }

    public function testOffPeakMultiplierAppliedOnce(): void
    {
        $rates = new UnitRates(unit: Rater::UNIT_UNIFIED, any: '5.00');
        $offPeak = OffPeakPolicy::fromMinutes(22 * 60 + 30, 1 * 60 + 30, '0.5');

        $inside = (new DateTimeImmutable('2026-01-01 23:30:00 UTC'));
        $outside = (new DateTimeImmutable('2026-01-01 12:00:00 UTC'));

        self::assertSame('0.437500', Rater::cost(self::tokens(175000), $rates, $offPeak, $inside), 'внутри окна — множитель 0.5');
        self::assertSame('0.875000', Rater::cost(self::tokens(175000), $rates, null, $inside), 'без окна — базовая стоимость');
        self::assertSame('0.875000', Rater::cost(self::tokens(175000), $rates, $offPeak, $outside), 'за окном — без множителя');
    }

    public function testUnknownUnitRejected(): void
    {
        $rates = new UnitRates(unit: 'per_1k_chars', any: '1.0');

        $this->expectException(DomainException::class);
        Rater::cost(self::tokens(1), $rates, null, new DateTimeImmutable());
    }
}
