<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\CostCalculator;
use App\Domain\OffPeakWindow;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private const RATES = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    public function testPerKindMatchesFixtureE1(): void
    {
        // 1 200 000×3.0 + 800 000×12.0 + 2 500 000×0.3 + 83 333×3.75 = 14 262 498.75 → /1M → 14.26249875 → 14.262499
        self::assertSame(
            '14.262499',
            CostCalculator::eventCost('per_1m_tokens', self::RATES, 1200000, 800000, 2500000, 83333, null, 0),
        );
    }

    public function testPerKindMatchesFixtureE2(): void
    {
        // Событие ровно на границе valid_from уже охватывается (проверяется отдельно), здесь — чистая арифметика.
        self::assertSame(
            '11.000000',
            CostCalculator::eventCost('per_1m_tokens', ['input' => '1.5', 'output' => '8.0', 'cache_read' => '0.15', 'cache_write' => '1.875'], 2000000, 1000000, 0, 0, null, 0),
        );
    }

    public function testUnifiedScheme(): void
    {
        self::assertSame(
            '8.400000',
            CostCalculator::eventCost('per_1m_tokens_unified', ['any' => '2.400000'], 3000000, 500000, 0, 0, null, 0),
        );
    }

    public function testOffPeakMultipliesAndRoundsOnce(): void
    {
        $window = OffPeakWindow::create(990, 30, '0.5');

        $base = CostCalculator::eventCost('per_1m_tokens', self::RATES, 1000000, 0, 0, 0, null, 900);
        $discountedOutside = CostCalculator::eventCost('per_1m_tokens', self::RATES, 1000000, 0, 0, 0, $window, 900);
        $discountedInside = CostCalculator::eventCost('per_1m_tokens', self::RATES, 1000000, 0, 0, 0, $window, 1000);

        self::assertSame('3.000000', $base);
        self::assertSame('3.000000', $discountedOutside, 'Вне окна стоимость не меняется');
        self::assertSame('1.500000', $discountedInside);
    }

    public function testUnknownUnitIsRejected(): void
    {
        $this->expectException(DomainException::class);
        CostCalculator::eventCost('per_1k_tokens', ['any' => '1'], 1, 0, 0, 0, null, 0);
    }

    public function testMissingRateIsRejected(): void
    {
        $this->expectException(DomainException::class);
        CostCalculator::eventCost('per_1m_tokens', ['input' => '1'], 1, 1, 0, 0, null, 0);
    }

    public function testMinuteOfDayUtcIsComputedInUtc(): void
    {
        $local = new DateTimeImmutable('2026-09-15T16:30:00+00:00', new DateTimeZone('UTC'));
        self::assertSame(990, CostCalculator::minuteOfDayUtc($local));

        // Тот же абсолютный момент, записанный в часовом поясе UTC+2.
        $plusTwo = new DateTimeImmutable('2026-09-15T18:30:00+02:00', new DateTimeZone('+02:00'));
        self::assertSame(990, CostCalculator::minuteOfDayUtc($plusTwo));
    }
}
