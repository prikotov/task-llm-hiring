<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\BillingUnit;
use App\Domain\CostCalculator;
use App\Domain\Model;
use App\Domain\PriceRow;
use App\Domain\Provider;
use App\Domain\UsageEvent;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    public function test_per_token_example_from_task(): void
    {
        $provider = Provider::make('prism', 'Prism AI');
        $provider->setOffPeak('16:30', '00:30', '0.5');
        $model = Model::make($provider, 'lumen-8');
        $row = PriceRow::make($provider, $model, new \DateTimeImmutable('2026-08-01T00:00:00Z'));
        $row->update(new \DateTimeImmutable('2026-08-01T00:00:00Z'), 'USD', BillingUnit::PerToken, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $event = new UsageEvent(
            id: null,
            provider: 'prism',
            providerRequestId: 'e1',
            model: 'lumen-8',
            user: 'u-alice',
            ts: new \DateTimeImmutable('2026-09-15T11:00:00Z'),
            tokensInput: 1200000,
            tokensOutput: 800000,
            tokensCacheRead: 2500000,
            tokensCacheWrite: 83333,
        );

        self::assertSame('14.262499', CostCalculator::calculate($row, $event));
    }

    public function test_unified_unit(): void
    {
        $provider = Provider::make('sfera', 'Sfera');
        $model = Model::make($provider, 'gyro-pro');
        $row = PriceRow::make($provider, $model, new \DateTimeImmutable('2026-08-01T00:00:00Z'));
        $row->update(new \DateTimeImmutable('2026-08-01T00:00:00Z'), 'USD', BillingUnit::Unified, ['any' => '2.400000']);

        $event = new UsageEvent(
            id: null,
            provider: 'sfera',
            providerRequestId: 'e3',
            model: 'gyro-pro',
            user: 'u-alice',
            ts: new \DateTimeImmutable('2026-09-20T17:10:00Z'),
            tokensInput: 3000000,
            tokensOutput: 500000,
            tokensCacheRead: 0,
            tokensCacheWrite: 0,
        );

        self::assertSame('8.400000', CostCalculator::calculate($row, $event));
    }

    public function test_off_peak_multiplier_applied(): void
    {
        $provider = Provider::make('acme', 'Acme');
        $provider->setOffPeak('16:30', '00:30', '0.5');
        $model = Model::make($provider, 'm1');
        $row = PriceRow::make($provider, $model, new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $row->update(new \DateTimeImmutable('2026-01-01T00:00:00Z'), 'USD', BillingUnit::PerToken, [
            'input' => '1.000000',
            'output' => '1.000000',
            'cache_read' => '1.000000',
            'cache_write' => '1.000000',
        ]);

        $makeEvent = static fn (string $ts): UsageEvent => new UsageEvent(
            id: null,
            provider: 'acme',
            providerRequestId: $ts,
            model: 'm1',
            user: 'u',
            ts: new \DateTimeImmutable($ts),
            tokensInput: 1000000,
            tokensOutput: 0,
            tokensCacheRead: 0,
            tokensCacheWrite: 0,
        );

        self::assertSame('0.500000', CostCalculator::calculate($row, $makeEvent('2026-01-15T17:00:00Z')));
        self::assertSame('1.000000', CostCalculator::calculate($row, $makeEvent('2026-01-15T11:00:00Z')));
    }

    public function test_half_up_rounding_of_submicro_dollars(): void
    {
        $provider = Provider::make('p', 'P');
        $model = Model::make($provider, 'm');
        $row = PriceRow::make($provider, $model, new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $row->update(new \DateTimeImmutable('2026-01-01T00:00:00Z'), 'USD', BillingUnit::PerToken, [
            'input' => '3.750000',
            'output' => '0',
            'cache_read' => '0',
            'cache_write' => '0',
        ]);

        $event = new UsageEvent(
            id: null,
            provider: 'p',
            providerRequestId: 'x',
            model: 'm',
            user: 'u',
            ts: new \DateTimeImmutable('2026-01-15T11:00:00Z'),
            tokensInput: 1,
            tokensOutput: 0,
            tokensCacheRead: 0,
            tokensCacheWrite: 0,
        );

        // 1 * 3.75 / 1e6 = 0.00000375 → 0.000004
        self::assertSame('0.000004', CostCalculator::calculate($row, $event));
    }
}
