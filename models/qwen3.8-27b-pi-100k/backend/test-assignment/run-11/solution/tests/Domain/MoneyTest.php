<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Application\Exception\BillingException;
use App\Domain\Money;
use App\Domain\OffPeakWindow;
use App\Domain\PriceSheet;
use App\Domain\RateCalculator;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testArithmetic(): void
    {
        self::assertSame('3.500000', Money::of('1.5')->add(Money::of('2.0'))->format(6));
        self::assertSame('1.000000', Money::of('2.5')->subtract(Money::of('1.5'))->format(6));
        self::assertSame('42.500000', Money::of('2.5')->multiply(Money::of('17'))->format(6));
        self::assertSame('1.250000', Money::of('2.5')->divide(Money::of('2'))->format(6));
        self::assertSame('50.000000', Money::of('5000000')->divide(Money::of('100000'))->format(6));
    }

    public function testHalfUpRounding(): void
    {
        self::assertSame('1.00', Money::of('1.0')->round(2)->format(2));
        // 2.5 → 3 (HALF_UP / HALF_AWAY_FROM_ZERO)
        self::assertSame('3', Money::of('2.5')->round(0)->format(0));
        // -2.5 → -3 (HALF_UP / HALF_AWAY_FROM_ZERO)
        self::assertSame('-3', Money::of('-2.5')->round(0)->format(0));
        // 1.2345675 → 1.234568
        self::assertSame('1.234568', Money::of('1.2345675')->round(6)->format(6));
        // 1.2345674 → 1.234567
        self::assertSame('1.234567', Money::of('1.2345674')->round(6)->format(6));
        // 99.9999995 → 100.000000
        self::assertSame('100.000000', Money::of('99.9999995')->round(6)->format(6));
        // 0.4999999 → 0.499999 (HALF_UP at 6th digit)
        self::assertSame('0.499999', Money::of('0.4999994')->round(6)->format(6));
    }

    public function testTaskExample14262499(): void
    {
        $sum = Money::zero()
            ->add(Money::of('1200000')->multiply(Money::of('3.0'))->divide(Money::of('1000000')))
            ->add(Money::of('800000')->multiply(Money::of('12.0'))->divide(Money::of('1000000')))
            ->add(Money::of('2500000')->multiply(Money::of('0.3'))->divide(Money::of('1000000')))
            ->add(Money::of('83333')->multiply(Money::of('3.75'))->divide(Money::of('1000000')));
        $rounded = $sum->round(6);
        self::assertSame('14.262499', $rounded->format(6));
    }

    public function testZeroDivision(): void
    {
        $this->expectException(BillingException::class);
        Money::of('1')->divide(Money::zero());
    }

    public function testPrecisionNoRoundingAtIntermediateSteps(): void
    {
        self::assertSame('0.3', Money::of('0.1')->add(Money::of('0.2'))->toDecimalString());
        self::assertSame('0.30000000000000000000000000000000', Money::of('0.1')->add(Money::of('0.2'))->format(32));
    }

    public function testComparison(): void
    {
        self::assertSame(0, Money::of('1')->compareTo(Money::of('1')));
        self::assertSame(1, Money::of('1.000001')->compareTo(Money::of('1.000000')));
        self::assertSame(-1, Money::of('0.999999')->compareTo(Money::of('1.000000')));
    }

    public function testRejectFloats(): void
    {
        $this->expectException(\TypeError::class);
        Money::of(1.25);
    }

    public function testOffPeakMidnightCross(): void
    {
        $w = OffPeakWindow::fromParts(16 * 60 + 30, 0 * 60 + 30, Money::of('0.5'));
        self::assertTrue($w->contains(new \DateTimeImmutable('2026-01-01T16:30:00+00:00')));
        self::assertTrue($w->contains(new \DateTimeImmutable('2026-01-01T23:59:00+00:00')));
        self::assertTrue($w->contains(new \DateTimeImmutable('2026-01-02T00:29:59+00:00')));
        self::assertFalse($w->contains(new \DateTimeImmutable('2026-01-02T00:30:00+00:00')));
        self::assertFalse($w->contains(new \DateTimeImmutable('2026-01-01T16:29:59+00:00')));
        self::assertFalse($w->contains(new \DateTimeImmutable('2026-01-01T12:00:00+00:00')));
    }

    public function testRateCalculatorTokenScheme(): void
    {
        $calc = new RateCalculator();
        $sheet = new PriceSheet(
            'USD',
            'per_1m_tokens',
            [
                'input' => Money::of('3.0'),
                'output' => Money::of('12.0'),
                'cache_read' => Money::of('0.3'),
                'cache_write' => Money::of('3.75'),
            ]
        );
        $cost = $calc->calculate(1200000, 800000, 2500000, 83333, $sheet, null, new \DateTimeImmutable('2026-09-15T11:00:00+00:00'));
        self::assertSame('14.262499', $cost->format(6));
    }

    public function testRateCalculatorUnifiedScheme(): void
    {
        $calc = new RateCalculator();
        $sheet = new PriceSheet('USD', 'per_1m_tokens_unified', ['any' => Money::of('2.4')]);
        $cost = $calc->calculate(3000000, 500000, 0, 0, $sheet, null, new \DateTimeImmutable('2026-09-20T17:10:00+00:00'));
        self::assertSame('8.400000', $cost->format(6));
    }

    public function testRateCalculatorOffPeakMultiplier(): void
    {
        $calc = new RateCalculator();
        $sheet = new PriceSheet('USD', 'per_1m_tokens_unified', ['any' => Money::of('4.00')]);
        $w = OffPeakWindow::fromParts(16 * 60, 20 * 60, Money::of('0.5'));
        $in = $calc->calculate(1000000, 0, 0, 0, $sheet, $w, new \DateTimeImmutable('2026-09-20T17:10:00+00:00'));
        self::assertSame('2.000000', $in->format(6)); // 1M×4×0.5 = 2.0
        $out = $calc->calculate(1000000, 0, 0, 0, $sheet, $w, new \DateTimeImmutable('2026-09-20T10:10:00+00:00'));
        self::assertSame('4.000000', $out->format(6));
    }

    public function testRateCalculatorZeroTokens(): void
    {
        $calc = new RateCalculator();
        $sheet = new PriceSheet('USD', 'per_1m_tokens_unified', ['any' => Money::of('2.4')]);
        $c = $calc->calculate(0, 0, 0, 0, $sheet, null, new \DateTimeImmutable('2026-01-01T00:00:00+00:00'));
        self::assertSame('0.000000', $c->format(6));
    }

    public function testRateCalculatorRejectsNegativeTokens(): void
    {
        $calc = new RateCalculator();
        $sheet = new PriceSheet('USD', 'per_1m_tokens', [
            'input' => Money::of('1'), 'output' => Money::of('1'),
            'cache_read' => Money::of('1'), 'cache_write' => Money::of('1'),
        ]);
        $this->expectException(BillingException::class);
        $calc->calculate(-1, 0, 0, 0, $sheet, null, new \DateTimeImmutable('2026-01-01T00:00:00+00:00'));
    }

    public function testMoneyStringRejectsInvalid(): void
    {
        $this->expectException(BillingException::class);
        Money::of('abc');
    }

    public function testMoneyInvalidScale(): void
    {
        $this->expectException(BillingException::class);
        Money::of('1')->round(-1);
    }
}
