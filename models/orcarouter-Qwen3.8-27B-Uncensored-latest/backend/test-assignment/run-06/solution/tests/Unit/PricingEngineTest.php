<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Domain\PricingEngine;
use PHPUnit\Framework\TestCase;

final class PricingEngineTest extends TestCase
{
    private PricingEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new PricingEngine();
    }

    public function testPerTokenSchemeMatchesTaskExample(): void
    {
        // e1 из TASK.md: 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75 / 1M = 14.26249875
        $cost = $this->engine->eventCostBeforeRounding(
            1200000,
            800000,
            2500000,
            83333,
            PricingEngine::UNIT_PER_TOKEN,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            null,
            null,
            null,
            '11:00',
        );

        self::assertSame('14.2624987500000000', $cost);
    }

    public function testUnifiedScheme(): void
    {
        // e3 из TASK.md: (3M + 0.5M) × 2.4 / 1M = 8.4
        $cost = $this->engine->eventCostBeforeRounding(
            3000000,
            500000,
            0,
            0,
            PricingEngine::UNIT_UNIFIED,
            ['any' => '2.400000'],
            null,
            null,
            null,
            '17:10',
        );

        self::assertSame('8.4000000000000000', $cost);
    }

    public function testOffPeakMultiplierAppliedInsideWindow(): void
    {
        $base = $this->engine->eventCostBeforeRounding(
            1000000, 0, 0, 0,
            PricingEngine::UNIT_PER_TOKEN,
            ['input' => '0.400000', 'output' => '1.600000', 'cache_read' => '0.040000', 'cache_write' => '0.500000'],
            null, null, null,
            '17:10',
        );
        $offPeak = $this->engine->eventCostBeforeRounding(
            1000000, 0, 0, 0,
            PricingEngine::UNIT_PER_TOKEN,
            ['input' => '0.400000', 'output' => '1.600000', 'cache_read' => '0.040000', 'cache_write' => '0.500000'],
            '16:30', '00:30', '0.5',
            '17:10',
        );
        $peak = $this->engine->eventCostBeforeRounding(
            1000000, 0, 0, 0,
            PricingEngine::UNIT_PER_TOKEN,
            ['input' => '0.400000', 'output' => '1.600000', 'cache_read' => '0.040000', 'cache_write' => '0.500000'],
            '16:30', '00:30', '0.5',
            '11:00',
        );

        self::assertSame('0.4000000000000000', $base);
        self::assertSame('0.2000000000000000', $offPeak);
        self::assertSame('0.4000000000000000', $peak);
    }

    public function testUnknownUnitRejected(): void
    {
        $this->expectException(DomainException::class);
        $this->engine->eventCostBeforeRounding(
            1, 0, 0, 0,
            'per_10k_tokens',
            ['any' => '1.000000'],
            null, null, null,
            '11:00',
        );
    }

    public function testMissingRateKeyRejected(): void
    {
        $this->expectException(DomainException::class);
        $this->engine->eventCostBeforeRounding(
            1, 0, 0, 0,
            PricingEngine::UNIT_PER_TOKEN,
            ['input' => '1.0'],
            null, null, null,
            '11:00',
        );
    }
}
