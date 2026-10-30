<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\CostCalculator;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\RateUnit;
use App\Domain\ValueObject\OffPeak;
use App\Domain\ValueObject\TokenUsage;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private CostCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new CostCalculator();
    }

    private function price(RateUnit $unit, array $rates, string $validFrom = '2026-08-01T00:00:00Z'): PriceRow
    {
        $row = new PriceRow();
        $row->setProvider('prism');
        $row->setModel('lumen-8');
        $row->setValidFrom(new \DateTimeImmutable($validFrom, new \DateTimeZone('UTC')));
        $row->set('USD', $unit, $rates);

        return $row;
    }

    private function tokens(int ...$byKind): TokenUsage
    {
        $kinds = ['input', 'output', 'cache_read', 'cache_write'];

        return TokenUsage::fromKinds(array_combine($kinds, $byKind));
    }

    /** Пример e1 из TASK.md: 14.26249875 → 14.262499. */
    public function testPerTokenSchemeE1(): void
    {
        $price = $this->price(RateUnit::Per1mTokens, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        $tokens = $this->tokens(1_200_000, 800_000, 2_500_000, 83_333);
        $cost = $this->calculator->compute($price, $tokens, null, new \DateTimeImmutable('2026-09-15T11:00:00Z'));

        self::assertSame('14.262499', $cost->format6());
    }

    /** Пример e2: 11.000000. */
    public function testPerTokenSchemeE2(): void
    {
        $price = $this->price(RateUnit::Per1mTokens, [
            'input' => '1.500000',
            'output' => '8.000000',
            'cache_read' => '0.150000',
            'cache_write' => '1.875000',
        ]);
        $cost = $this->calculator->compute($price, $this->tokens(2_000_000, 1_000_000, 0, 0), null, new \DateTimeImmutable('2026-09-15T12:00:00Z'));

        self::assertSame('11.000000', $cost->format6());
    }

    /** Пример e3: unified 3.5M × 2.4 = 8.400000. */
    public function testUnifiedSchemaE3(): void
    {
        $price = $this->price(RateUnit::Per1mTokensUnified, ['any' => '2.400000']);
        $cost = $this->calculator->compute($price, $this->tokens(3_000_000, 500_000, 0, 0), null, new \DateTimeImmutable('2026-09-20T17:10:00Z'));

        self::assertSame('8.400000', $cost->format6());
    }

    public function testOffPeakMultiplierAppliedToWholeEvent(): void
    {
        $price = $this->price(RateUnit::Per1mTokens, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        $offPeak = new OffPeak('16:30', '00:30', '0.5');
        $tokens = $this->tokens(1_000_000, 0, 0, 0);

        $peak = $this->calculator->compute($price, $tokens, $offPeak, new \DateTimeImmutable('2026-09-15T10:00:00Z'));
        $off = $this->calculator->compute($price, $tokens, $offPeak, new \DateTimeImmutable('2026-09-15T17:00:00Z'));

        self::assertSame('3.000000', $peak->format6());
        self::assertSame('1.500000', $off->format6());
    }

    public function testAllZeroTokensCostZero(): void
    {
        $price = $this->price(RateUnit::Per1mTokensUnified, ['any' => '2.400000']);
        $cost = $this->calculator->compute($price, $this->tokens(0, 0, 0, 0), null, new \DateTimeImmutable('2026-09-15T11:00:00Z'));

        self::assertSame('0.000000', $cost->format6());
    }
}
