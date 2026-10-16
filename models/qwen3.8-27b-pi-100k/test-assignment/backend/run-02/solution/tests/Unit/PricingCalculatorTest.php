<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Billing\OffPeakPolicy;
use App\Domain\Billing\PricingCalculator;
use App\Domain\Billing\TokenUsage;
use App\Domain\Exception\BillingException;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calculator;
    private Model $model;

    protected function setUp(): void
    {
        $this->calculator = new PricingCalculator();
        $this->model = new Model(new Provider('prism', 'Prism AI'), 'lumen-8');
    }

    private function perTokenRow(string $validFrom, array $overrides = []): Price
    {
        $rates = array_merge([
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ], $overrides);

        return new Price($this->model, new DateTimeImmutable($validFrom), 'USD', Price::UNIT_PER_TOKEN, $rates);
    }

    private function unifiedRow(string $validFrom, string $any): Price
    {
        return new Price($this->model, new DateTimeImmutable($validFrom), 'USD', Price::UNIT_UNIFIED, ['any' => $any]);
    }

    /**
     * Пример из fixtures: событие 2026-09-15T11:00:00Z по строке 2026-08-01.
     * cost = (1.2M*3 + 0.8M*12 + 2.5M*0.3 + 83333*3.75) / 1M
     *      = (3.6 + 9.6 + 0.75 + 0.31249875) = 14.26249875 -> HALF_UP 6 -> 14.262499.
     */
    public function testPerTokenRateMatchesTaskExample(): void
    {
        $cost = $this->calculator->price(
            $this->perTokenRow('2026-08-01T00:00:00Z'),
            new TokenUsage(1_200_000, 800_000, 2_500_000, 83_333),
        );

        self::assertSame('14.262499', $cost);
    }

    public function testHalfUpAtSixthDecimal(): void
    {
        // 0.8M * 9.6 / 1M = 7.68 — ровно; но 10001 * 3.5 / 1M = 0.0350035 -> 0.035004? Нет:
        // 0.0350035: 6 знаков 0.035003, хвост 0.0000005 -> HALF_UP -> 0.035004.
        $cost = $this->calculator->price(
            $this->perTokenRow('2026-08-01T00:00:00Z', ['input' => '3.5']),
            new TokenUsage(10_001, 0, 0, 0),
        );
        self::assertSame('0.035004', $cost);
    }

    public function testUnifiedRate(): void
    {
        // (3M + 0.5M) * 2.4 / 1M = 8.4
        $cost = $this->calculator->price(
            $this->unifiedRow('2026-08-01T00:00:00Z', '2.400000'),
            new TokenUsage(3_000_000, 500_000, 0, 0),
        );
        self::assertSame('8.400000', $cost);
    }

    public function testOffPeakAppliesInsideWindow(): void
    {
        $policy = new OffPeakPolicy('16:30', '00:30', '0.5');

        $inside = $this->calculator->price(
            $this->perTokenRow('2026-08-01T00:00:00Z', ['input' => '1.5', 'output' => '8.0']),
            new TokenUsage(1_200_000, 800_000, 0, 0),
            $policy,
            new DateTimeImmutable('2026-09-15T17:10:00Z'),
        );
        self::assertSame('4.100000', $inside); // 8.2 * 0.5

        $outside = $this->calculator->price(
            $this->perTokenRow('2026-08-01T00:00:00Z', ['input' => '1.5', 'output' => '8.0']),
            new TokenUsage(1_200_000, 800_000, 0, 0),
            $policy,
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );
        self::assertSame('8.200000', $outside); // без скидки
    }

    public function testZeroTokensCostZero(): void
    {
        $cost = $this->calculator->price(
            $this->perTokenRow('2026-08-01T00:00:00Z'),
            new TokenUsage(0, 0, 0, 0),
        );
        self::assertSame('0.000000', $cost);
    }

    public function testUnknownUnitThrows(): void
    {
        $row = new Price($this->model, new DateTimeImmutable('2026-08-01T00:00:00Z'), 'USD', 'per_token', []);

        $this->expectException(BillingException::class);
        $this->calculator->price($row, new TokenUsage(1, 1, 1, 1));
    }
}
