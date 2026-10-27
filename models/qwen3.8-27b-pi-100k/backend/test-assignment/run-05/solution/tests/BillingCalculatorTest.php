<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\BillingCalculator;
use App\Domain\Money;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class BillingCalculatorTest extends TestCase
{
    private static function provider(string $code, ?array $offPeak = null): Provider
    {
        $provider = new Provider($code, $code);
        if ($offPeak !== null) {
            $provider->setOffPeak($offPeak['window_start'], $offPeak['window_end'], $offPeak['multiplier'] ?? '0.5');
        }

        return $provider;
    }

    private static function row(string $unit, array $rates, DateTimeImmutable $validFrom): PriceRow
    {
        $model = new Model(new Provider('p', 'P'), 'm');
        $row = new PriceRow($model, $validFrom);
        $row->setCurrency('USD');

        if ($unit === 'per_1m_tokens') {
            $row->applyUnitAndRates(
                $unit,
                $rates['input'] ?? null,
                $rates['output'] ?? null,
                $rates['cache_read'] ?? null,
                $rates['cache_write'] ?? null,
                null,
            );
        } else {
            $row->applyUnitAndRates($unit, null, null, null, null, $rates['any']);
        }

        return $row;
    }

    public function testPerTokenPricingFromTaskExample(): void
    {
        // Пример e1 из задания: 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M = 14.26249875
        $row = self::row('per_1m_tokens', [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ], new DateTimeImmutable('2026-08-01T00:00:00Z', new DateTimeZone('UTC')));

        $exact = BillingCalculator::computeExact($row, null, new DateTimeImmutable('2026-09-15T11:00:00Z', new DateTimeZone('UTC')), 1200000, 800000, 2500000, 83333);
        self::assertNotNull($exact);
        self::assertSame('14.262499', Money::round6($exact));
    }

    public function testUnifiedPricingFromTaskExample(): void
    {
        // Пример e3: (3M + 0.5M) × 2.4 = 8.4
        $row = self::row('per_1m_tokens_unified', ['any' => '2.400000'], new DateTimeImmutable('2026-08-01T00:00:00Z', new DateTimeZone('UTC')));

        $exact = BillingCalculator::computeExact($row, null, new DateTimeImmutable('2026-09-20T17:10:00Z', new DateTimeZone('UTC')), 3000000, 500000, 0, 0);
        self::assertNotNull($exact);
        self::assertSame('8.400000', Money::round6($exact));
    }

    public function testOffPeakMultiplierApplied(): void
    {
        $provider = self::provider('prism', ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0.5']);
        $row = self::row('per_1m_tokens_unified', ['any' => '1.000000'], new DateTimeImmutable('2026-01-01T00:00:00Z', new DateTimeZone('UTC')));

        // 1M токенов × 1.0 = 1.0; в оф-пике ×0.5 = 0.5
        $off = new DateTimeImmutable('2026-09-15T18:00:00Z', new DateTimeZone('UTC'));
        $exact = BillingCalculator::computeExact($row, $provider, $off, 1000000, 0, 0, 0);
        self::assertNotNull($exact);
        self::assertSame('0.500000', Money::round6($exact));

        $peak = new DateTimeImmutable('2026-09-15T11:00:00Z', new DateTimeZone('UTC'));
        $exact = BillingCalculator::computeExact($row, $provider, $peak, 1000000, 0, 0, 0);
        self::assertNotNull($exact);
        self::assertSame('1.000000', Money::round6($exact));
    }

    public function testUnknownUnitIsUnrateable(): void
    {
        $model = new Model(new Provider('p', 'P'), 'm');
        $row = new PriceRow($model, new DateTimeImmutable('2026-01-01T00:00:00Z', new DateTimeZone('UTC')));

        self::assertNull(BillingCalculator::computeExact($row, null, new DateTimeImmutable('2026-09-15T00:00:00Z', new DateTimeZone('UTC')), 1, 1, 1, 1));
    }
}
