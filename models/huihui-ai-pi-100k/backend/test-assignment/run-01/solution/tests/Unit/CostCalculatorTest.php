<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Billing\CostCalculator;
use App\Domain\Decimal;
use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use App\Domain\Unit;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    private const SCALE_HINT = 'входной контекст: ставки за 1 000 000 токенов';

    private function priceRow(
        ?OffPeakWindow $offPeak,
        string $validFrom = '2026-08-01T00:00:00Z',
        Unit $unit = Unit::PerToken,
        array $rates = ['input' => '3', 'output' => '12', 'cache_read' => '0.3', 'cache_write' => '3.75'],
    ): PriceRow {
        $provider = new Provider('prism', 'Prism AI', $offPeak);
        $model = new Model($provider, 'lumen-8');

        return new PriceRow($provider, $model, new \DateTimeImmutable($validFrom, new \DateTimeZone('UTC')), 'USD', $unit, $rates);
    }

    private function at(string $hhmm): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-09-15T' . $hhmm . ':00+00:00');
    }

    public function testPerTokenExactValueFixtureE1(): void
    {
        // e1: 1 200 000 in / 800 000 out / 2 500 000 cache_read / 83 333 cache_write
        //    при ставках 3 / 12 / 0.3 / 3.75 за 1M — точное значение до финального
        //    HALF_UP-округления:
        //    3.6 + 9.6 + 0.75 + 0.31249875 = 14.26249875
        $calc = new CostCalculator();
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);
        $value = $calc->priceCost($this->priceRow(null), $usage, $this->at('11:00'), null);

        self::assertTrue($value->equals('14.26249875'), self::SCALE_HINT);
        self::assertSame('14.262499', $value->roundHalfUp(6)->value());
    }

    public function testOffPeakMultiplierAppliedOnlyInsideWindow(): void
    {
        $calc = new CostCalculator();
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);
        $offPeak = OffPeakWindow::create('16:30', '00:30', '0.5');

        // 17:10 — в окне: 14.26249875 * 0.5 = 7.131249375
        $inside = $calc->priceCost($this->priceRow($offPeak), $usage, $this->at('17:10'), $offPeak);
        self::assertTrue($inside->equals('7.131249375'));

        // 23:30 — в окне (переход через полночь)
        $inside2 = $calc->priceCost($this->priceRow($offPeak), $usage, $this->at('23:30'), $offPeak);
        self::assertTrue($inside2->equals('7.131249375'));

        // 00:20 — в окне
        $inside3 = $calc->priceCost($this->priceRow($offPeak), $usage, $this->at('00:20'), $offPeak);
        self::assertTrue($inside3->equals('7.131249375'));

        // 12:00 — вне окна (это и есть сценарий e2 в fixtures/usage.jsonl)
        $outside = $calc->priceCost($this->priceRow($offPeak), $usage, $this->at('12:00'), $offPeak);
        self::assertTrue($outside->equals('14.26249875'));

        // 00:30 — граница, оконце исключительно
        $edge = $calc->priceCost($this->priceRow($offPeak), $usage, $this->at('00:30'), $offPeak);
        self::assertTrue($edge->equals('14.26249875'));
    }

    public function testUnifiedScheme(): void
    {
        // gyro-pro: 3 000 000 + 500 000 токенов, единая ставка 2.4 за 1M → 8.4
        $calc = new CostCalculator();
        $row = $this->priceRow(
            null,
            unit: Unit::PerTokenUnified,
            rates: ['any' => '2.4'],
        );
        $usage = new TokenUsage(3000000, 500000, 0, 0);

        $value = $calc->priceCost($row, $usage, $this->at('17:10'), null);
        self::assertTrue($value->equals('8.4'));
        self::assertSame('8.400000', $value->roundHalfUp(6)->value());
    }

    public function testZeroUsageIsZero(): void
    {
        $calc = new CostCalculator();
        $usage = new TokenUsage(0, 0, 0, 0);
        $value = $calc->priceCost($this->priceRow(null), $usage, $this->at('11:00'), null);
        self::assertTrue($value->isZero());
    }

    public function testPerTokenRowRequiresAllFourRates(): void
    {
        $provider = new Provider('p', 'P');
        $model = new Model($provider, 'm');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/cache_write/');
        new PriceRow(
            $provider,
            $model,
            new \DateTimeImmutable('2026-08-01T00:00:00Z', new \DateTimeZone('UTC')),
            'USD',
            Unit::PerToken,
            ['input' => '1', 'output' => '2', 'cache_read' => '0.1'],
        );
    }

    public function testSchemeMismatchIsError(): void
    {
        $provider = new Provider('p', 'P');
        $model = new Model($provider, 'm');
        $ts = new \DateTimeImmutable('2026-08-01T00:00:00Z', new \DateTimeZone('UTC'));

        // per_token-строка c единой ставкой: не хватает раздельных ставок
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/input/');
        new PriceRow($provider, $model, $ts, 'USD', Unit::PerToken, ['any' => '2.4']);

        // unified-строка без ставки any
        $this->expectException(DomainException::class);
        new PriceRow($provider, $model, $ts, 'USD', Unit::PerTokenUnified, ['input' => '1']);
    }
}
