<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\BillingComputer;
use App\Domain\Money;
use App\Domain\OffPeakWindow;
use App\Domain\PriceUnit;
use PHPUnit\Framework\TestCase;

final class BillingComputerTest extends TestCase
{
    private BillingComputer $computer;

    protected function setUp(): void
    {
        $this->computer = new BillingComputer();
    }

    private function ts(string $hhmm): \DateTimeImmutable
    {
        return new \DateTimeImmutable("2026-09-15T{$hhmm}:00Z", new \DateTimeZone('UTC'));
    }

    /**
     * Событие e1 из задания: per_1m_tokens, без оф-пика (11:00 вне окна 16:30–00:30).
     */
    public function testPerTokenEventFromExample(): void
    {
        $cost = $this->computer->calculate(
            1_200_000,
            800_000,
            2_500_000,
            83_333,
            PriceUnit::Per1mTokens,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            new OffPeakWindow('16:30', '00:30', '0.5'),
            $this->ts('11:00'),
        );

        self::assertSame('14.26249875', $cost->format(8));
        self::assertSame('14.262499', $cost->roundHalfUp()->format(6));
    }

    /**
     * Событие e3 из задания: unified-схема ((in+out+cr+cw) × rate_any / 1M).
     */
    public function testUnifiedEventFromExample(): void
    {
        $cost = $this->computer->calculate(
            3_000_000,
            500_000,
            0,
            0,
            PriceUnit::Per1mTokensUnified,
            ['any' => '2.400000'],
            null,
            $this->ts('17:10'),
        );

        self::assertSame('8.400000', $cost->format(6));
    }

    /**
     * Оф-пик: то же событие в окне — итог умножается на множитель.
     */
    public function testOffPeakMultiplierApplied(): void
    {
        $window = new OffPeakWindow('16:30', '00:30', '0.5');

        $peak = $this->computer->calculate(
            1_000_000, 500_000, 0, 0,
            PriceUnit::Per1mTokens,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            $window,
            $this->ts('10:00'),
        );
        $offPeak = $this->computer->calculate(
            1_000_000, 500_000, 0, 0,
            PriceUnit::Per1mTokens,
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            $window,
            $this->ts('20:00'),
        );

        self::assertSame('9.000000', $peak->format(6));
        self::assertSame('4.500000', $offPeak->format(6));
    }

    public function testZeroTokensYieldZeroCost(): void
    {
        $cost = $this->computer->calculate(
            0, 0, 0, 0,
            PriceUnit::Per1mTokensUnified,
            ['any' => '2.400000'],
            new OffPeakWindow('00:00', '23:59', '0.5'),
            $this->ts('12:00'),
        );

        self::assertSame('0.000000', $cost->format(6));
    }
}
