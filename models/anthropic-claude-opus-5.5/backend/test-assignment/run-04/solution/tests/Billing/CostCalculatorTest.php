<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Billing\CostCalculator;
use App\Domain\Pricing\Model;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PricingUnit;
use App\Domain\Pricing\Provider;
use App\Domain\Usage\TokenUsage;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private function provider(?OffPeakWindow $offPeak = null): Provider
    {
        return new Provider('prism', 'Prism AI', $offPeak);
    }

    private function model(): Model
    {
        return new Model('lumen-8');
    }

    private function row(Provider $provider, Model $model, string $validFrom, string $unit, array $rates): PriceRow
    {
        return new PriceRow(
            $provider,
            $model,
            new DateTimeImmutable($validFrom, new DateTimeZone('UTC')),
            PricingUnit::fromString($unit),
            $rates,
        );
    }

    private function at(string $iso): DateTimeImmutable
    {
        return new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }

    public function testPerTokenSchemeFromTaskExample(): void
    {
        $provider = $this->provider(OffPeakWindow::fromStrings('16:30', '00:30', '0.5'));
        $model = $this->model();
        $rows = [$this->row($provider, $model, '2026-08-01T00:00:00Z', 'per_1m_tokens', [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ])];

        $cost = (new CostCalculator())->calculate(
            $provider,
            $model,
            $rows,
            $this->at('2026-09-15T11:00:00Z'),
            new TokenUsage(1_200_000, 800_000, 2_500_000, 83_333),
        );

        self::assertNotNull($cost);
        self::assertSame('14.262499', $cost->fixed(6));
    }

    public function testPriceSwitchIsInclusiveOnValidFrom(): void
    {
        $provider = $this->provider();
        $model = $this->model();
        $rows = [
            $this->row($provider, $model, '2026-08-01T00:00:00Z', 'per_1m_tokens', [
                'input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000',
            ]),
            $this->row($provider, $model, '2026-09-15T12:00:00Z', 'per_1m_tokens', [
                'input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.150000', 'cache_write' => '1.875000',
            ]),
        ];

        $before = (new CostCalculator())->calculate($provider, $model, $rows, $this->at('2026-09-15T11:59:59Z'), new TokenUsage(1_000_000, 0, 0, 0));
        $exact = (new CostCalculator())->calculate($provider, $model, $rows, $this->at('2026-09-15T12:00:00Z'), new TokenUsage(1_000_000, 0, 0, 0));

        self::assertNotNull($before);
        self::assertNotNull($exact);
        self::assertSame('3.000000', $before->fixed(6));
        self::assertSame('1.500000', $exact->fixed(6));
    }

    public function testUnifiedSchemeUsesAnyRate(): void
    {
        $provider = $this->provider();
        $model = new Model('gyro-pro');
        $rows = [$this->row($provider, $model, '2026-08-01T00:00:00Z', 'per_1m_tokens_unified', ['any' => '2.400000'])];

        $cost = (new CostCalculator())->calculate(
            $provider,
            $model,
            $rows,
            $this->at('2026-09-20T17:10:00Z'),
            new TokenUsage(3_000_000, 500_000, 0, 0),
        );

        self::assertNotNull($cost);
        self::assertSame('8.400000', $cost->fixed(6));
    }

    public function testOffPeakDiscountAppliesToWholeEvent(): void
    {
        $provider = $this->provider(OffPeakWindow::fromStrings('16:30', '00:30', '0.5'));
        $model = new Model('lumen-mini');
        $rows = [$this->row($provider, $model, '2026-08-01T00:00:00Z', 'per_1m_tokens', [
            'input' => '0.400000', 'output' => '1.600000', 'cache_read' => '0.040000', 'cache_write' => '0.500000',
        ])];

        $peak = (new CostCalculator())->calculate($provider, $model, $rows, $this->at('2026-09-21T16:00:00Z'), new TokenUsage(1_000_000, 0, 0, 0));
        $offPeak = (new CostCalculator())->calculate($provider, $model, $rows, $this->at('2026-09-21T17:00:00Z'), new TokenUsage(1_000_000, 0, 0, 0));

        self::assertNotNull($peak);
        self::assertNotNull($offPeak);
        self::assertSame('0.400000', $peak->fixed(6));
        self::assertSame('0.200000', $offPeak->fixed(6));
    }

    public function testEventIsUnratedWithoutEarlierPriceRow(): void
    {
        $provider = $this->provider();
        $model = $this->model();
        $rows = [$this->row($provider, $model, '2026-08-01T00:00:00Z', 'per_1m_tokens', [
            'input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000',
        ])];

        $cost = (new CostCalculator())->calculate($provider, $model, $rows, $this->at('2026-07-01T00:00:00Z'), new TokenUsage(1, 0, 0, 0));

        self::assertNull($cost);
    }

    public function testZeroTokensCostNothing(): void
    {
        $provider = $this->provider();
        $model = $this->model();
        $rows = [$this->row($provider, $model, '2026-08-01T00:00:00Z', 'per_1m_tokens', [
            'input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000',
        ])];

        $cost = (new CostCalculator())->calculate($provider, $model, $rows, $this->at('2026-09-01T00:00:00Z'), new TokenUsage());

        self::assertNotNull($cost);
        self::assertSame('0.000000', $cost->fixed(6));
    }
}
