<?php

declare(strict_types=1);

namespace Tests\Domain\Billing;

use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\RateCalculator;
use App\Domain\Usage\UsageEvent;
use PHPUnit\Framework\TestCase;

final class RateCalculatorTest extends TestCase
{
    private function event(int $in, int $out, int $cr, int $cw, string $timeUtc = '2026-09-15T11:00:00+00:00'): UsageEvent
    {
        $e = new UsageEvent();
        $e->provider = 'prism';
        $e->providerRequestId = 'test';
        $e->modelCode = 'lumen-8';
        $e->user = 'u-test';
        $e->ts = new \DateTimeImmutable($timeUtc);
        $e->tokensInput = $in;
        $e->tokensOutput = $out;
        $e->tokensCacheRead = $cr;
        $e->tokensCacheWrite = $cw;

        return $e;
    }

    public function testPerTokenExactCostMatchesTaskExampleE1(): void
    {
        $e = $this->event(1_200_000, 800_000, 2_500_000, 83_333);
        $cost = RateCalculator::eventCost($e, 'per_1m_tokens', [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        self::assertSame('14.262499', $cost); // 3.6 + 9.6 + 0.75 + 0.31249875 → HALF_UP до 6 знаков
    }

    public function testUnifiedPricingSumsAllTokens(): void
    {
        $e = $this->event(3_000_000, 500_000, 0, 0);
        $cost = RateCalculator::eventCost($e, 'per_1m_tokens_unified', ['any' => '2.400000']);
        self::assertSame('8.400000', $cost); // 3.5M × 2.4
    }

    public function testOffPeakMultiplierAppliesInsideWindow(): void
    {
        $window = new OffPeakWindow('16:30', '00:30');
        $rates = ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.500000', 'cache_write' => '0.500000'];

        // peak: множитель не применяется
        self::assertSame('11.000000', RateCalculator::eventCost(
            $this->event(2_000_000, 1_000_000, 0, 0, '2026-09-15T12:00:00+00:00'),
            'per_1m_tokens',
            $rates,
            $window,
            '0.5',
        ));

        // off-peak: (2M×1.5 + 1M×8) = 11 → ×0.5 = 5.5
        self::assertSame('5.500000', RateCalculator::eventCost(
            $this->event(2_000_000, 1_000_000, 0, 0, '2026-09-15T23:30:00+00:00'),
            'per_1m_tokens',
            $rates,
            $window,
            '0.5',
        ));

        // граница окна: конец 00:30 не включён
        self::assertSame('11.000000', RateCalculator::eventCost(
            $this->event(2_000_000, 1_000_000, 0, 0, '2026-09-15T00:30:00+00:00'),
            'per_1m_tokens',
            $rates,
            $window,
            '0.5',
        ));
    }

    public function testRoundingHappensOnceAtTheEnd(): void
    {
        // 83333 × 3.75 / 1e6 = 0.31249875 → HALF_UP до 6 знаков = 0.312499
        // (один раз в конце, а не на каждом слагаемом).
        $e = $this->event(0, 0, 0, 83_333);
        $cost = RateCalculator::eventCost($e, 'per_1m_tokens', [
            'input' => '0', 'output' => '0', 'cache_read' => '0', 'cache_write' => '3.750000',
        ]);
        self::assertSame('0.312499', $cost);
    }
}
