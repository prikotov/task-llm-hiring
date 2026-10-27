<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Billing\BillingService;
use App\Domain\Billing\Cost;
use App\Domain\Billing\ProviderInfo;
use App\Domain\Billing\TokenMix;
use App\Domain\Pricing\BillingUnit;
use App\Domain\Pricing\OffPeakPolicy;
use App\Domain\Pricing\PriceRow;
use App\Domain\Usage\UsageEvent;
use App\Tests\Billing\InMemoryCatalog;
use App\Tests\Billing\InMemoryEvents;
use App\Tests\Billing\InMemoryStore;
use PHPUnit\Framework\TestCase;

/**
 * Юнит-тесты ядра тарификации (TASK.md «Правила тарификации»):
 *  - точная стоимость per_1m_tokens (Σ tokens × rate / 1e6);
 *  - точная стоимость per_1m_tokens_unified (Σ tokens × rate / 1e6);
 *  - оф-пик:  multiplier, если ts входит в [window_start, window_end);
 *  - единственное округление:  HALF_UP 6 знаков.
 */
final class BillingServiceTest extends TestCase
{
    public function testPerTokenCostSummation(): void
    {
        $svc = $this->service();

        $row = new PriceRow(
            providerCode: 'prism',
            modelCode: 'm1',
            validFrom: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            currency: 'USD',
            unit: BillingUnit::PER_1M_TOKENS,
            rates: [
                'input' => Cost::make('3.000000'),
                'output' => Cost::make('12.000000'),
                'cache_read' => Cost::make('0.300000'),
                'cache_write' => Cost::make('3.750000'),
            ],
        );

        $event = new UsageEvent(
            provider: 'prism',
            providerRequestId: 'r1',
            model: 'm1',
            user: 'u1',
            ts: new \DateTimeImmutable('2026-09-15T11:00:00Z'),
            tokens: new TokenMix(1_200_000, 800_000, 2_500_000, 83_333),
        );

        $cost = $svc->calculateCost($event, $row);
        self::assertSame('14.262499', $cost->toSixDecimalsTruncated());
    }

    public function testOffpeakAppliesInsideWindow(): void
    {
        $svc = $this->service(
            new ProviderInfo('prism', new OffPeakPolicy('16:30', '00:30', Cost::make('0.5'))),
        );

        $row = new PriceRow(
            providerCode: 'prism',
            modelCode: 'm1',
            validFrom: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            currency: 'USD',
            unit: BillingUnit::PER_1M_TOKENS_UNIFIED,
            rates: ['any' => Cost::make('2.000000')],
        );

        // 17:00 UTC — inside [16:30, 00:30)
        $event = new UsageEvent(
            provider: 'prism',
            providerRequestId: 'r1',
            model: 'm1',
            user: 'u1',
            ts: new \DateTimeImmutable('2026-09-15T17:00:00Z'),
            tokens: new TokenMix(1_000_000, 0, 0, 0),
        );

        // 1M tokens @ 2 USD/M = 2 USD, × 0.5 = 1 USD
        $cost = $svc->calculateCost($event, $row);
        self::assertSame('1.000000', $cost->toSixDecimalsTruncated());
    }

    public function testOffpeakDoesNotApplyOutsideWindow(): void
    {
        $svc = $this->service(
            new ProviderInfo('prism', new OffPeakPolicy('16:30', '00:30', Cost::make('0.5'))),
        );

        $row = new PriceRow(
            providerCode: 'prism',
            modelCode: 'm1',
            validFrom: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            currency: 'USD',
            unit: BillingUnit::PER_1M_TOKENS_UNIFIED,
            rates: ['any' => Cost::make('2.000000')],
        );

        // 12:00 UTC — outside [16:30, 00:30)
        $event = new UsageEvent(
            provider: 'prism',
            providerRequestId: 'r1',
            model: 'm1',
            user: 'u1',
            ts: new \DateTimeImmutable('2026-09-15T12:00:00Z'),
            tokens: new TokenMix(1_000_000, 0, 0, 0),
        );

        // 1M tokens @ 2 USD/M = 2 USD (no offpeak)
        $cost = $svc->calculateCost($event, $row);
        self::assertSame('2.000000', $cost->toSixDecimalsTruncated());
    }

    private function service(?ProviderInfo $provider = null): BillingService
    {
        return new BillingService(
            new InMemoryCatalog([], $provider),
            new InMemoryStore(),
            new InMemoryEvents(\Closure::fromCallable(fn (): array => [])),
        );
    }
}
