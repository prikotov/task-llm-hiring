<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Billing\OffPeakPolicy;
use App\Domain\Exception\BillingException;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class OffPeakPolicyTest extends TestCase
{
    private static function time(string $value): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-09-15T'.$value.':00Z');
    }

    public function testWindowWithoutMidnightCrossing(): void
    {
        $policy = new OffPeakPolicy('09:00', '17:00', '0.5');

        self::assertFalse($policy->applies(self::time('08:59')));
        self::assertTrue($policy->applies(self::time('09:00')));  // начало включительно
        self::assertTrue($policy->applies(self::time('10:30')));
        self::assertFalse($policy->applies(self::time('17:00'))); // конец исключительно
        self::assertFalse($policy->applies(self::time('17:01')));
        self::assertFalse($policy->applies(self::time('00:15')));
    }

    public function testWindowCrossingMidnight(): void
    {
        $policy = new OffPeakPolicy('16:30', '00:30', '0.5');

        self::assertFalse($policy->applies(self::time('16:29')));
        self::assertTrue($policy->applies(self::time('16:30')));
        self::assertTrue($policy->applies(self::time('23:59')));
        self::assertTrue($policy->applies(self::time('00:00')));
        self::assertTrue($policy->applies(self::time('00:29')));
        self::assertFalse($policy->applies(self::time('00:30')));
        self::assertFalse($policy->applies(self::time('15:59')));
    }

    public function testDegenerateWindowIsEmpty(): void
    {
        $policy = new OffPeakPolicy('12:00', '12:00', '0.5');
        self::assertFalse($policy->applies(self::time('12:00')));
    }

    public function testMultiplierIsExposed(): void
    {
        self::assertSame('0.5', (new OffPeakPolicy('00:00', '23:59', '0.5'))->multiplier());
    }

    public function testInvalidWindowThrows(): void
    {
        $this->expectException(BillingException::class);
        new OffPeakPolicy('24:00', '00:30', '0.5');
    }

    public function testInvalidMultiplierThrows(): void
    {
        $this->expectException(BillingException::class);
        new OffPeakPolicy('09:00', '17:00', '-0.5');
    }
}
