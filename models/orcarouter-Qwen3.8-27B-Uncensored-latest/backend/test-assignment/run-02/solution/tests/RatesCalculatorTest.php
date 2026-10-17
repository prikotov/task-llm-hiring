<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\RatesCalculator;
use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RatesCalculatorTest extends TestCase
{
    private RatesCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new RatesCalculator();
    }

    private function ts(string $s): DateTimeImmutable
    {
        return new DateTimeImmutable($s, new DateTimeZone('UTC'));
    }

    /** e1 из TASK.md: 14.26249875 → 14.262499. */
    #[Test]
    public function perTokenSchemeMatchesSpecificationExample(): void
    {
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);
        $rates = ['input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75'];

        self::assertSame('14.262499', $this->calculator->finalCost($usage, RatesCalculator::UNIT_PER_TOKEN, $rates, null, $this->ts('2026-09-15T11:00:00Z')));
    }

    #[Test]
    public function unifiedSchemeMatchesSpecificationExample(): void
    {
        $usage = new TokenUsage(3000000, 500000, 0, 0);
        self::assertSame('8.400000', $this->calculator->finalCost($usage, RatesCalculator::UNIT_UNIFIED, ['any' => '2.4'], null, $this->ts('2026-09-20T17:10:00Z')));
    }

    #[Test]
    public function offPeakMultiplierAppliesToWholeEvent(): void
    {
        $usage = new TokenUsage(1000000, 0, 0, 0);
        $rates = ['input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75'];
        $window = OffPeakWindow::fromRaw('16:30', '00:30', '0.5');

        self::assertTrue($window->inWindow($this->ts('2026-09-15T16:30:00Z')));
        self::assertTrue($window->inWindow($this->ts('2026-09-15T23:59:59Z')));
        self::assertTrue($window->inWindow($this->ts('2026-09-15T00:29:59Z')));
        self::assertFalse($window->inWindow($this->ts('2026-09-15T00:30:00Z')));
        self::assertFalse($window->inWindow($this->ts('2026-09-15T16:29:59Z')));

        self::assertSame('1.500000', $this->calculator->finalCost($usage, RatesCalculator::UNIT_PER_TOKEN, $rates, $window, $this->ts('2026-09-15T19:00:00Z')));
        self::assertSame('3.000000', $this->calculator->finalCost($usage, RatesCalculator::UNIT_PER_TOKEN, $rates, $window, $this->ts('2026-09-15T10:00:00Z')));
    }

    #[Test]
    public function zeroTokensCostsZero(): void
    {
        $usage = new TokenUsage(0, 0, 0, 0);
        self::assertSame('0.000000', $this->calculator->finalCost($usage, RatesCalculator::UNIT_UNIFIED, ['any' => '2.4'], null, $this->ts('2026-01-01T00:00:00Z')));
    }
}
