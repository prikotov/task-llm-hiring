<?php

declare(strict_types=1);

namespace App\Tests\Domain\Pricing;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceLine;
use App\Domain\Catalog\Provider;
use App\Domain\Pricing\PriceScheme;
use App\Domain\Pricing\RateCalculator;
use App\Domain\Usage\UsageEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Фиксированные значения из TASK.md (пример расчёта).
 */
final class RateCalculatorTest extends TestCase
{
    private RateCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new RateCalculator();
    }

    #[Test]
    public function specExampleE1PerKindRates(): void
    {
        $provider = $this->prism();
        $line = $this->line(
            $provider,
            '2026-08-01T00:00:00Z',
            PriceScheme::PerToken,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
        );
        $event = $this->event(
            'prism', 'e1', 'lumen-8', 'alice',
            '2026-09-15T11:00:00Z',
            ['tokens_input' => 1200000, 'tokens_output' => 800000, 'tokens_cache_read' => 2500000, 'tokens_cache_write' => 83333],
        );

        self::assertSame(0, bccomp('14.26249875', $this->calc->rawCost($event, $line)));
        self::assertSame('14.262499', $this->calc->eventCost($event, $line, $provider));
    }

    #[Test]
    public function specExampleE2ValidFromIsInclusive(): void
    {
        $provider = $this->prism();
        $line = $this->line(
            $provider,
            '2026-09-15T12:00:00Z',
            PriceScheme::PerToken,
            ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.150000', 'cache_write' => '1.875000'],
        );
        // 12:00:00 — сам момент смены цены, уже новая строка.
        $event = $this->event(
            'prism', 'e2', 'lumen-8', 'bob',
            '2026-09-15T12:00:00Z',
            ['tokens_input' => 2000000, 'tokens_output' => 1000000, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0],
        );

        self::assertSame('11.000000', $this->calc->eventCost($event, $line, $provider));
    }

    #[Test]
    public function specExampleE3UnifiedRate(): void
    {
        $sfera = $this->provider('sfera');
        $line = $this->line($sfera, '2026-08-01T00:00:00Z', PriceScheme::PerTokenUnified, ['any' => '2.400000']);
        $event = $this->event(
            'sfera', 'e3', 'gyro-pro', 'alice',
            '2026-09-20T17:10:00Z',
            ['tokens_input' => 3000000, 'tokens_output' => 500000, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0],
        );

        self::assertSame('8.400000', $this->calc->eventCost($event, $line, $sfera));
    }

    #[Test]
    public function offPeakMultiplierIsAppliedWholeEvent(): void
    {
        // prism: окно 16:30-00:30, множитель 0.5.
        $provider = $this->prism();
        $line = $this->line(
            $provider,
            '2026-08-01T00:00:00Z',
            PriceScheme::PerToken,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
        );

        $peak = $this->event('prism', 'p', 'lumen-8', 'u', '2026-09-15T11:00:00Z',
            ['tokens_input' => 1000000, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0]);
        self::assertSame('3.000000', $this->calc->eventCost($peak, $line, $provider));

        $peakByBoundary = $this->event('prism', 'b', 'lumen-8', 'u', '2026-09-15T00:30:00Z',
            ['tokens_input' => 1000000, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0]);
        // 00:30 — конец окна (не включён) → пик.
        self::assertSame('3.000000', $this->calc->eventCost($peakByBoundary, $line, $provider));

        $off = $this->event('prism', 'o', 'lumen-8', 'u', '2026-09-15T17:00:00Z',
            ['tokens_input' => 1000000, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0]);
        self::assertSame('1.500000', $this->calc->eventCost($off, $line, $provider));

        $offAfterMidnight = $this->event('prism', 'm', 'lumen-8', 'u', '2026-09-15T00:29:59Z',
            ['tokens_input' => 1000000, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0]);
        self::assertSame('1.500000', $this->calc->eventCost($offAfterMidnight, $line, $provider));
    }

    #[Test]
    public function roundingIsHalfUpToSixPlaces(): void
    {
        $provider = $this->provider('nopeak');

        $exact = $this->line($provider, '2026-08-01T00:00:00Z', PriceScheme::PerToken,
            ['input' => '0.9000004', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0']);
        $event = $this->event('nopeak', 'x1', 'm', 'u', '2026-09-15T11:00:00Z',
            ['tokens_input' => 1000000, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0]);
        self::assertSame('0.900000', $this->calc->eventCost($event, $exact, $provider));

        $halfUp = $this->line($provider, '2026-08-01T00:00:00Z', PriceScheme::PerToken,
            ['input' => '0.9000005', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0']);
        self::assertSame('0.900001', $this->calc->eventCost($event, $halfUp, $provider));
    }

    private function provider(string $code): Provider
    {
        return new Provider($code);
    }

    private function prism(): Provider
    {
        $provider = $this->provider('prism');
        $provider->setOffPeak(16 * 60 + 30, 0 * 60 + 30, '0.5');

        return $provider;
    }

    private function line(Provider $provider, string $validFrom, PriceScheme $scheme, array $rates): PriceLine
    {
        $model = new Model($provider, 'lumen-8');
        $line = new PriceLine($provider, $model, new \DateTimeImmutable($validFrom));
        $line->setPricingData('USD', $scheme, $rates);

        return $line;
    }

    /** @param array{tokens_input:int, tokens_output:int, tokens_cache_read:int, tokens_cache_write:int} $tokens */
    private function event(string $provider, string $id, string $model, string $user, string $ts, array $tokens): UsageEvent
    {
        return new UsageEvent($provider, $id, $model, $user, new \DateTimeImmutable($ts), $tokens);
    }
}
