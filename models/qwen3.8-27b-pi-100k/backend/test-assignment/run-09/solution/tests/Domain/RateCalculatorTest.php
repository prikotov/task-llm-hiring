<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\RateCalculator;
use App\Domain\Billing\Unit;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\TokenModel;
use App\Entity\UsageEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RateCalculatorTest extends TestCase
{
    private RateCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new RateCalculator();
    }

    public function testPer1mTokensSplitRates(): void
    {
        // Пример из фикстур: prism/lumen-8, e1 (вне оф-пика)
        // 1.2M×3.0 + 0.8M×12.0 + 2.5M×0.3 + 83333×3.75 = 14.26249875 -> 14.262499
        [$row, $event] = $this->makeSplitModel(
            ['input' => '3.0', 'output' => '12.0', 'cache_read' => '0.3', 'cache_write' => '3.75'],
            1200000, 800000, 2500000, 83333,
        );

        self::assertSame('14.262499', $this->calc->calculate($event, $row, null)->amount());
    }

    public function testUnifiedRate(): void
    {
        // sfera/gyro-pro, unified 2.4: (3.0M + 0.5M) × 2.4 = 8.4 -> 8.400000
        $provider = new Provider();
        $provider->setCode('sfera');
        $model = new TokenModel();
        $model->setCode('gyro-pro');
        $model->setProvider($provider);

        $row = new PriceRow();
        $row->setProvider($provider);
        $row->setModel($model);
        $row->setValidFrom(new DateTimeImmutable('2026-08-01T00:00:00Z'));
        $row->setUnit(Unit::PER_1M_TOKENS_UNIFIED);
        $row->replaceRates(['any' => '2.4']);

        $event = new UsageEvent();
        $event->setProvider($provider);
        $event->setModel($model);
        $event->setTs(new DateTimeImmutable('2026-09-20T17:10:00Z'));
        $event->setTokensInput(3000000);
        $event->setTokensOutput(500000);

        self::assertSame('8.400000', $this->calc->calculate($event, $row, null)->amount());
    }

    public function testOffPeakMultiplierAppliedWhenInWindow(): void
    {
        // В окна: 1.0M input × 1.0, без скидки = 1.0; со скидкой 0.5 = 0.5
        [$row, $event, $offPeak] = $this->makeSplitModelWithProvider(
            ['16:30', '00:30', '0.5'],
            ['input' => '1.0', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
            1000000, 0, 0, 0, '18:15:00Z',
        );

        self::assertNotNull($offPeak);
        self::assertSame('1.000000', $this->calc->calculate($event, $row, null)->amount());
        self::assertSame('0.500000', $this->calc->calculate($event, $row, $offPeak)->amount());
    }

    public function testOffPeakNotAppliedOutsideWindow(): void
    {
        // Время 10:00 вне окна 16:30-00:30 -> без скидки
        [$row, $event, $offPeak] = $this->makeSplitModelWithProvider(
            ['16:30', '00:30', '0.5'],
            ['input' => '1.0', 'output' => '0', 'cache_read' => '0', 'cache_write' => '0'],
            1000000, 0, 0, 0, '10:00:00Z',
        );

        self::assertNotNull($offPeak);
        self::assertSame('1.000000', $this->calc->calculate($event, $row, $offPeak)->amount());
    }

    /**
     * @return array{0: PriceRow, 1: UsageEvent}
     */
    private function makeSplitModel(array $rates, int $in, int $out, int $cr, int $cw): array
    {
        $provider = new Provider();
        $provider->setCode('prism');

        $model = new TokenModel();
        $model->setCode('lumen-8');
        $model->setProvider($provider);

        $row = new PriceRow();
        $row->setProvider($provider);
        $row->setModel($model);
        $row->setValidFrom(new DateTimeImmutable('2026-08-01T00:00:00Z'));
        $row->setUnit(Unit::PER_1M_TOKENS);
        $row->replaceRates($rates);

        $event = new UsageEvent();
        $event->setProvider($provider);
        $event->setModel($model);
        $event->setTs(new DateTimeImmutable('2026-09-15T11:00:00Z'));
        $event->setTokensInput($in);
        $event->setTokensOutput($out);
        $event->setTokensCacheRead($cr);
        $event->setTokensCacheWrite($cw);

        return [$row, $event];
    }

    /**
     * @return array{0: PriceRow, 1: UsageEvent, 2: OffPeakWindow|null}
     */
    private function makeSplitModelWithProvider(
        array $offPeak,
        array $rates,
        int $in,
        int $out,
        int $cr,
        int $cw,
        string $hmi,
    ): array {
        $provider = new Provider();
        $provider->setCode('prism');
        $provider->setOffPeak($offPeak[0], $offPeak[1], $offPeak[2]);

        $model = new TokenModel();
        $model->setCode('lumen-8');
        $model->setProvider($provider);

        $row = new PriceRow();
        $row->setProvider($provider);
        $row->setModel($model);
        $row->setValidFrom(new DateTimeImmutable('2026-08-01T00:00:00Z'));
        $row->setUnit(Unit::PER_1M_TOKENS);
        $row->replaceRates($rates);

        $event = new UsageEvent();
        $event->setProvider($provider);
        $event->setModel($model);
        $event->setTs(new DateTimeImmutable('2026-09-15T'.$hmi));
        $event->setTokensInput($in);
        $event->setTokensOutput($out);
        $event->setTokensCacheRead($cr);
        $event->setTokensCacheWrite($cw);

        $offPeakWindow = OffPeakWindow::fromProvider($provider);

        return [$row, $event, $offPeakWindow];
    }
}
