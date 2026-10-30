<?php

declare(strict_types=1);

namespace App\Tests\Domain\Billing;

use App\Domain\Billing\EventPricer;
use App\Domain\Billing\OffPeak;
use App\Domain\Billing\PriceRowSpec;
use App\Domain\Billing\TokenUsage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Золотые примеры расчёта (TASK.md, «Пример расчёта», fixtures/*):
 * ставки и токены — как в fixtures/prices.json и fixtures/usage.jsonl.
 */
final class EventPricerTest extends TestCase
{
    private EventPricer $pricer;

    protected function setUp(): void
    {
        $this->pricer = new EventPricer();
    }

    /** Строка lumen-8 с 2026-08-01. */
    private function oldRow(): PriceRowSpec
    {
        return PriceRowSpec::perToken('3.000000', '12.000000', '0.300000', '3.750000');
    }

    /** Строка lumen-8 с 2026-09-15T12:00. */
    private function newRow(): PriceRowSpec
    {
        return PriceRowSpec::perToken('1.500000', '8.000000', '0.150000', '1.875000');
    }

    private function prismOffPeak(): OffPeak
    {
        return OffPeak::fromWindow('16:30', '00:30', '0.5');
    }

    #[Test]
    public function fixtureEventE1(): void
    {
        // 11:00 — пик: 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M
        // = 14.26249875 → 14.262499
        $cost = $this->pricer->price(
            $this->oldRow(),
            TokenUsage::create(1200000, 800000, 2500000, 83333),
            $this->prismOffPeak(),
            new \DateTimeImmutable('2026-09-15T11:00:00Z'),
        );

        self::assertSame('14.262499', $cost);
    }

    #[Test]
    public function fixtureEventE2(): void
    {
        // 12:00:00 — строка с valid_from 12:00 включительно: 2M×1.5 + 1M×8
        $cost = $this->pricer->price(
            $this->newRow(),
            TokenUsage::create(2000000, 1000000, 0, 0),
            $this->prismOffPeak(),
            new \DateTimeImmutable('2026-09-15T12:00:00Z'),
        );

        self::assertSame('11.000000', $cost);
    }

    #[Test]
    public function fixtureEventE3Unified(): void
    {
        // sfera/gyro-pro, unified rate 2.4; 3.5M токенов → 8.400000
        $row = PriceRowSpec::unified('2.400000');

        $cost = $this->pricer->price(
            $row,
            TokenUsage::create(3000000, 500000, 0, 0),
            null,
            new \DateTimeImmutable('2026-09-20T17:10:00Z'),
        );

        self::assertSame('8.400000', $cost);
    }

    #[Test]
    public function offPeakMultiplierAppliedBeforeRounding(): void
    {
        // (1M×1.5 + 1M×8 + 1M×0.15 + 1M×1.875) = 11.525; ×0.5 = 5.7625
        $cost = $this->pricer->price(
            $this->newRow(),
            TokenUsage::create(1000000, 1000000, 1000000, 1000000),
            $this->prismOffPeak(),
            new \DateTimeImmutable('2026-09-15T17:15:00Z'),
        );

        self::assertSame('5.762500', $cost);
    }
}
