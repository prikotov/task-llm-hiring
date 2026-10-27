<?php

declare(strict_types=1);

namespace App\Tests\Domain\Billing;

use App\Domain\Billing\OffPeak;
use App\Domain\Billing\PricingCalculator;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    /** @var array{input:int,output:int,cache_read:int,cache_write:int} */
    private array $tokens;

    protected function setUp(): void
    {
        $this->tokens = ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333];
    }

    private function ts(string $iso): DateTimeImmutable
    {
        return new DateTimeImmutable($iso, new DateTimeZone('UTC'));
    }

    #[Test]
    public function perTokenKindsMatchesTaskExample(): void
    {
        $cost = PricingCalculator::computeEventCost(
            $this->tokens,
            'per_1m_tokens',
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            null,
            $this->ts('2026-09-15T11:00:00Z'),
        );

        self::assertSame('14.262499', $cost);
    }

    #[Test]
    public function offPeakMultiplierIsAppliedToWholeEvent(): void
    {
        $offPeak = new OffPeak('16:30', '00:30', '0.5');
        $base = PricingCalculator::computeEventCost(
            ['input' => 1000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            'per_1m_tokens',
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            null,
            $this->ts('2026-09-16T12:00:00Z'),
        );
        $reduced = PricingCalculator::computeEventCost(
            ['input' => 1000000, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
            'per_1m_tokens',
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            $offPeak,
            $this->ts('2026-09-16T17:00:00Z'),
        );

        self::assertSame('3.000000', $base);
        self::assertSame('1.500000', $reduced);
    }

    #[Test]
    public function unifiedRateSumsAllTokenKinds(): void
    {
        $cost = PricingCalculator::computeEventCost(
            ['input' => 3000000, 'output' => 500000, 'cache_read' => 0, 'cache_write' => 0],
            'per_1m_tokens_unified',
            ['any' => '2.400000'],
            null,
            $this->ts('2026-09-20T17:10:00Z'),
        );

        self::assertSame('8.400000', $cost);
    }

    #[Test]
    public function unknownUnitIsRejected(): void
    {
        $this->expectException(\DomainException::class);

        PricingCalculator::computeEventCost($this->tokens, 'per_byte', ['x' => '1'], null, $this->ts('2026-09-15T12:00:00Z'));
    }
}
