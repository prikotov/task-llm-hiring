<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Pricing\Entity\Price;
use App\Domain\Pricing\Entity\ProviderModel;
use App\Domain\Pricing\Service\PricingCalculator;
use App\Domain\Pricing\ValueObject\OffPeakWindow;
use App\Domain\Pricing\ValueObject\PricingUnit;
use App\Domain\Provider\Entity\Provider;
use App\Domain\Usage\Entity\UsageEvent;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PricingCalculator();
    }

    public function testPerTokenPricingAndEventRounding(): void
    {
        $provider = new Provider('prism', 'Prism AI');
        $price = $this->price($provider, PricingUnit::Per1mTokens, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        $event = $this->event($provider, '2026-09-15T11:00:00Z', 1200000, 800000, 2500000, 83333);

        self::assertSame('14.262499', $this->calculator->calculate($event, $price));
    }

    public function testUnifiedPricing(): void
    {
        $provider = new Provider('sfera', 'Sfera Cloud');
        $price = $this->price($provider, PricingUnit::Per1mTokensUnified, ['any' => '2.400000']);
        $event = $this->event($provider, '2026-09-20T17:10:00Z', 3000000, 500000, 0, 0);

        self::assertSame('8.400000', $this->calculator->calculate($event, $price));
    }

    public function testOffPeakMultiplierIsAppliedToWholeEvent(): void
    {
        $provider = new Provider('prism', 'Prism AI');
        $provider->changeOffPeak(new OffPeakWindow('16:30', '00:30', '0.5'));
        $price = $this->price($provider, PricingUnit::Per1mTokens, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        $event = $this->event($provider, '2026-09-15T18:00:00Z', 1200000, 800000, 2500000, 83333);

        self::assertSame('7.131249', $this->calculator->calculate($event, $price));
    }

    /**
     * @param array<string, string> $rates
     */
    private function price(Provider $provider, PricingUnit $unit, array $rates): Price
    {
        $model = new ProviderModel($provider, 'test-model');

        return new Price(
            $model,
            new DateTimeImmutable('2026-08-01T00:00:00Z', new DateTimeZone('UTC')),
            'USD',
            $unit,
            $rates,
        );
    }

    private function event(
        Provider $provider,
        string $ts,
        int $input,
        int $output,
        int $cacheRead,
        int $cacheWrite,
    ): UsageEvent {
        return new UsageEvent(
            $provider->code(),
            'request-1',
            'test-model',
            'user-1',
            new DateTimeImmutable($ts, new DateTimeZone('UTC')),
            $input,
            $output,
            $cacheRead,
            $cacheWrite,
        );
    }
}
