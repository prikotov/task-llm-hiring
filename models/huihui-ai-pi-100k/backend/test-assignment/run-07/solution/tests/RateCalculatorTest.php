<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\OffPeak;
use App\Domain\RateCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Расчёт стоимости события: две схемы тарификации, оф-пик как множитель
 * всего события, итоговое HALF_UP-округление до 6 знаков.
 */
final class RateCalculatorTest extends TestCase
{
    private RateCalculator $calculator;
    private \DateTimeZone $utc;

    protected function setUp(): void
    {
        $this->calculator = new RateCalculator();
        $this->utc = new \DateTimeZone('UTC');
    }

    public function testPerTokenScheme(): void
    {
        // e1 из фикстур: (1.2M*3 + 0.8M*12 + 2.5M*0.3 + 83333*3.75) / 1M = 14.26249875 -> 14.262499
        $rate = 14.262499;
        $calc = $this->calculator->costUsd(
            ['unit' => 'per_1m_tokens', 'rates' => [
                'input' => '3.000000', 'output' => '12.000000',
                'cache_read' => '0.300000', 'cache_write' => '3.750000',
            ]],
            ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
            null,
            new \DateTimeImmutable('2026-09-15T11:00:00Z', $this->utc)
        );
        self::assertSame('14.262499', $calc);
    }

    public function testUnifiedScheme(): void
    {
        // e3: (3.0M + 0.5M) * 2.4 / 1M = 8.4
        $calc = $this->calculator->costUsd(
            ['unit' => 'per_1m_tokens_unified', 'rates' => ['any' => '2.400000']],
            ['input' => 3000000, 'output' => 500000, 'cache_read' => 0, 'cache_write' => 0],
            null,
            new \DateTimeImmutable('2026-09-20T17:10:00Z', $this->utc)
        );
        self::assertSame('8.400000', $calc);
    }

    public function testOffPeakMultiplierAppliesWholeEvent(): void
    {
        // 800M * 1.0 / 1M = 800.0; * 0.25 = 200.0
        $offPeak = OffPeak::fromProvider('00:00', '23:59', '0.25');
        $calc = $this->calculator->costUsd(
            ['unit' => 'per_1m_tokens', 'rates' => [
                'input' => '1.000000', 'output' => '1.000000',
                'cache_read' => '1.000000', 'cache_write' => '1.000000',
            ]],
            ['input' => 800000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            $offPeak,
            new \DateTimeImmutable('2026-09-15T21:00:00Z', $this->utc)
        );
        self::assertSame('200.000000', $calc);
    }

    public function testOffPeakNotAppliedOutsideWindow(): void
    {
        // то же событие, но день — 0.25 не применяется
        $offPeak = OffPeak::fromProvider('16:30', '00:30', '0.25');
        $calc = $this->calculator->costUsd(
            ['unit' => 'per_1m_tokens', 'rates' => [
                'input' => '1.000000', 'output' => '1.000000',
                'cache_read' => '1.000000', 'cache_write' => '1.000000',
            ]],
            ['input' => 800000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            $offPeak,
            new \DateTimeImmutable('2026-09-15T09:30:00Z', $this->utc)
        );
        self::assertSame('800.000000', $calc);
    }

    public function testNoOffPeakMeansNull(): void
    {
        $offPeak = OffPeak::fromProvider(null, null, null);
        $calc = $this->calculator->costUsd(
            ['unit' => 'per_1m_tokens', 'rates' => [
                'input' => '1.000000', 'output' => '1.000000',
                'cache_read' => '1.000000', 'cache_write' => '1.000000',
            ]],
            ['input' => 1000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            $offPeak,
            new \DateTimeImmutable('2026-09-15T21:00:00Z', $this->utc)
        );
        self::assertSame('1.000000', $calc);
    }

    public function testUnknownUnitThrows(): void
    {
        $this->expectException(\LogicException::class);
        $this->calculator->costUsd(
            ['unit' => 'weird', 'rates' => []],
            ['input' => 1, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            null,
            new \DateTimeImmutable('2026-09-15T21:00:00Z', $this->utc)
        );
    }

    public function testRoundingHalfUpToSix(): void
    {
        // 1 * 0.3333335 / 1 = 0.3333335 -> 0.333334 (HALF_UP)
        $calc = $this->calculator->costUsd(
            ['unit' => 'per_1m_tokens_unified', 'rates' => ['any' => '0.3333335']],
            ['input' => 1000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            null,
            new \DateTimeImmutable('2026-09-15T21:00:00Z', $this->utc)
        );
        self::assertSame('0.333334', $calc);
    }
}
