<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\Enum\PricingUnit;
use App\Billing\Pricing\RateCalculator;
use App\Billing\ValueObject\Decimal;
use App\Billing\ValueObject\Price;
use App\Billing\ValueObject\TokenUsage;
use PHPUnit\Framework\TestCase;

final class RateCalculatorTest extends TestCase
{
    private const PER_1M = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    private function per1M(): Price
    {
        return Price::create(PricingUnit::Per1MTokens, self::PER_1M);
    }

    private function unified(string $any = '2.400000'): Price
    {
        return Price::create(PricingUnit::Per1MTokensUnified, ['any' => $any]);
    }

    public function testExampleFromSpec_e1(): void
    {
        // 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M = 14.26249875
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);

        self::assertSame(
            '14.26249875',
            (new RateCalculator())->cost($this->per1M(), $usage, null)->value(),
        );
    }

    public function testRoundAfterCostMatchesSpec(): void
    {
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);

        self::assertSame(
            '14.262499',
            (new RateCalculator())->cost($this->per1M(), $usage, null)
                ->round(6)
                ->toFixed(6),
        );
    }

    public function testUnifiedSchemaSumsAllTokenKinds(): void
    {
        // (3M + 0.5M) × 2.4 / 1M = 8.4
        $usage = new TokenUsage(3000000, 500000, 0, 0);

        self::assertSame(
            '8.400000',
            (new RateCalculator())->cost($this->unified(), $usage, null)->toFixed(6),
        );
    }

    public function testOffPeakMultiplierAppliesToWholeEvent(): void
    {
        // 1M input × 3.0 / 1M = 3.0; × 0.5 = 1.5
        $usage = new TokenUsage(1000000, 0, 0, 0);

        self::assertSame(
            '1.500000',
            (new RateCalculator())->cost($this->per1M(), $usage, Decimal::of('0.5'))->toFixed(6),
        );
    }

    public function testZeroTokensCostsZero(): void
    {
        $zero = new TokenUsage(0, 0, 0, 0);

        self::assertTrue(
            (new RateCalculator())->cost($this->per1M(), $zero, null)->isZero(),
            'событие с нулевыми токенами стоит ровно 0',
        );
    }

    public function testPer1MRequiresAllRates(): void
    {
        $this->expectException(\App\Billing\Import\ImportException::class);

        Price::create(PricingUnit::Per1MTokens, ['input' => '1.0']);
    }

    public function testUnknownUnitRejected(): void
    {
        $this->expectException(\App\Billing\Import\ImportException::class);

        \App\Billing\Enum\PricingUnit::fromString('per_1k_tokens');
    }
}
