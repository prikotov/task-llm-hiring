<?php

declare(strict_types=1);

namespace App\Tests\Application\Billing;

use App\Application\Billing\PricingCalculator;
use App\Domain\Money;
use App\Infrastructure\Provider;
use App\Infrastructure\UsageEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new PricingCalculator();
    }

    /**
     * Создаёт UsageEvent для теста (без БД).
     */
    private function createEvent(
        string $provider = 'prism',
        string $ts = '2026-09-15T11:00:00',
        int $tokensInput = 1200000,
        int $tokensOutput = 800000,
        int $cacheRead = 2500000,
        int $cacheWrite = 83333,
    ): UsageEvent {
        return new UsageEvent(
            provider: $provider,
            requestId: 'test-1',
            model: 'lumen-8',
            user: 'u-alice',
            ts: new \DateTimeImmutable($ts.'Z', new \DateTimeZone('UTC')),
            tokensInput: $tokensInput,
            tokensOutput: $tokensOutput,
            tokensCacheRead: $cacheRead,
            tokensCacheWrite: $cacheWrite,
        );
    }

    #[Test]
    public function testOffPeakWindowCheck(): void
    {
        $calc = $this->calc;

        // 11:00 UTC → 660 min → NOT in 16:30-00:30 (990-30)
        $ts = new \DateTimeImmutable('2026-09-15T11:00:00Z', new \DateTimeZone('UTC'));
        self::assertFalse($calc->inWindow($ts, '16:30', '00:30'));

        // 17:00 UTC → 1020 min → IN 16:30-00:30 (990 >= 1020 || ... → TRUE)
        $ts = new \DateTimeImmutable('2026-09-15T17:00:00Z', new \DateTimeZone('UTC'));
        self::assertTrue($calc->inWindow($ts, '16:30', '00:30'));

        // 00:30 UTC → 30 min → IN 16:30-00:30 (990 > 30, 30 < 30 → FALSE)
        $ts = new \DateTimeImmutable('2026-09-15T00:00:00Z', new \DateTimeZone('UTC'));
        self::assertTrue($calc->inWindow($ts, '16:30', '00:30'));

        // 00:30 itself → boundary check
        $ts = new \DateTimeImmutable('2026-09-15T00:30:00Z', new \DateTimeZone('UTC'));
        self::assertFalse($calc->inWindow($ts, '16:30', '00:30'));

        // 16:30 start → boundary check
        $ts = new \DateTimeImmutable('2026-09-15T16:30:00Z', new \DateTimeZone('UTC'));
        self::assertTrue($calc->inWindow($ts, '16:30', '00:30'));

        // 16:29 → NOT in window
        $ts = new \DateTimeImmutable('2026-09-15T16:29:00Z', new \DateTimeZone('UTC'));
        self::assertFalse($calc->inWindow($ts, '16:30', '00:30'));
    }

    #[Test]
    public function testApplyOffPeak(): void
    {
        $calc = $this->calc;

        $provider = new Provider(
            code: 'prism',
            name: 'Test',
            offPeakWindowStart: '16:30',
            offPeakWindowEnd: '00:30',
            offPeakMultiplier: '0.5',
        );

        // 11:00 — NOT in window
        $ts = new \DateTimeImmutable('2026-09-15T11:00:00Z', new \DateTimeZone('UTC'));
        $cost = $calc->applyOffPeak('10', $ts, $provider);
        self::assertSame('10', \App\Domain\Money::format($cost));

        // 17:10 — IN window
        $ts = new \DateTimeImmutable('2026-09-20T17:10:00Z', new \DateTimeZone('UTC'));
        $cost = $calc->applyOffPeak('10', $ts, $provider);
        self::assertSame('5', \App\Domain\Money::format($cost));
    }

    #[Test]
    public function testNoOffPeak(): void
    {
        $calc = $this->calc;

        $provider = new Provider(code: 'sfera', name: 'Sfera');
        $event = $this->createEvent(provider: 'sfera', ts: '2026-09-20T17:10:00',
            tokensInput: 3000000, tokensOutput: 500000, cacheRead: 0, cacheWrite: 0);

        $rowRates = ['any' => '2.4'];
        $unit = \App\Domain\PriceUnit::PER_1M_TOKENS_UNIFIED;

        // Create a fake PriceRow for testing
        $row = new \App\Infrastructure\PriceRow(
            provider: 'sfera',
            model: 'gyro-pro',
            validFrom: new \DateTimeImmutable('2026-08-01T00:00:00Z', new \DateTimeZone('UTC')),
            currency: 'USD',
            unit: $unit,
            rates: $rowRates,
        );

        $result = $calc->eventCost($event, $row, $provider);
        self::assertSame('8.4', \App\Domain\Money::format($result->roundedCost));
    }

    #[Test]
    public function testBoundaryInclusion(): void
    {
        $calc = $this->calc;

        // При 16:30 — В окне (включает)
        $ts = new \DateTimeImmutable('2026-09-15T16:30:00Z', new \DateTimeZone('UTC'));
        self::assertTrue($calc->inWindow($ts, '16:30', '00:30'));

        // При 00:30 — НЕ в окне (исключает)
        $ts = new \DateTimeImmutable('2026-09-15T00:30:00Z', new \DateTimeZone('UTC'));
        self::assertFalse($calc->inWindow($ts, '16:30', '00:30'));
    }
}
