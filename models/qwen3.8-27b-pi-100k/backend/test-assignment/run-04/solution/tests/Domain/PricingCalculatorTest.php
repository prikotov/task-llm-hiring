<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Money;
use App\Domain\OffPeak;
use App\Domain\PriceSpec;
use App\Domain\PricingCalculator;
use App\Exception\ImportException;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calc;
    private \DateTimeImmutable $ts;

    protected function setUp(): void
    {
        $this->calc = new PricingCalculator();
        $this->ts = new \DateTimeImmutable('2026-09-15T12:00:00Z', new \DateTimeZone('UTC'));
    }

    public function test_per_1m_example_from_task(): void
    {
        // e1 из TASK.md: 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M = 14.26249875 → 14.262499
        $price = PriceSpec::create(PriceSpec::UNIT_PER_1M, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        self::assertSame(
            '14.262499',
            $this->calc->rate($price, 1_200_000, 800_000, 2_500_000, 83_333, null, $this->ts),
        );
    }

    public function test_unified_example_from_task(): void
    {
        // e3 из TASK.md: unified 3.5M×2.4 = 8.4, у sfera нет оф-пик
        $price = PriceSpec::create(PriceSpec::UNIT_UNIFIED, ['any' => '2.400000']);

        self::assertSame(
            '8.400000',
            $this->calc->rate($price, 3_000_000, 500_000, 0, 0, null, $this->ts),
        );
    }

    public function test_off_peak_multiplier_applies_to_whole_event(): void
    {
        $price = PriceSpec::create(PriceSpec::UNIT_UNIFIED, ['any' => '2.400000']);
        $offPeak = OffPeak::create('16:30', '00:30', '0.5');

        // 17:10 в окне → ×0.5
        $inWindow = new \DateTimeImmutable('2026-09-20T17:10:00Z', new \DateTimeZone('UTC'));
        self::assertSame('4.200000', $this->calc->rate($price, 3_000_000, 500_000, 0, 0, $offPeak, $inWindow));

        // 10:00 вне окна → без скидки
        $outOfWindow = new \DateTimeImmutable('2026-09-20T10:00:00Z', new \DateTimeZone('UTC'));
        self::assertSame('8.400000', $this->calc->rate($price, 3_000_000, 500_000, 0, 0, $offPeak, $outOfWindow));
    }

    public function test_zero_tokens_is_zero_cost(): void
    {
        $price = PriceSpec::create(PriceSpec::UNIT_PER_1M, [
            'input' => '1.0', 'output' => '2.0', 'cache_read' => '0.1', 'cache_write' => '0.2',
        ]);

        self::assertSame('0.000000', $this->calc->rate($price, 0, 0, 0, 0, null, $this->ts));
    }

    public function test_unknown_unit_rejected(): void
    {
        $this->expectException(ImportException::class);
        PriceSpec::create('per_1k_tokens', ['any' => '1.0']);
    }

    public function test_wrong_rates_for_unit_rejected(): void
    {
        $this->expectException(ImportException::class);
        PriceSpec::create(PriceSpec::UNIT_PER_1M, ['any' => '1.0']);
    }

    public function test_non_decimal_rate_rejected(): void
    {
        $this->expectException(ImportException::class);
        PriceSpec::create(PriceSpec::UNIT_UNIFIED, ['any' => 1.5]); // number, а не string
    }

    public function test_negative_rate_rejected(): void
    {
        $this->expectException(ImportException::class);
        PriceSpec::create(PriceSpec::UNIT_UNIFIED, ['any' => '-1.0']);
    }
}
