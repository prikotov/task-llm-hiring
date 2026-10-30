<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Billing\Decimal;
use App\Domain\Billing\EventPricingService;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use PHPUnit\Framework\TestCase;

/**
 * Тесты тарификации событий (без БД): выбор действующей цены, расчёт стоимости
 * с учётом схемы (token/unified), оф-пик, единственное округление HALF_UP до 6.
 */
final class BillingEngineTest extends TestCase
{
    private function event(
        Provider $provider,
        Model $model,
        string $ts,
        int $in,
        int $out,
        int $cr = 0,
        int $cw = 0,
    ): UsageEvent {
        return new UsageEvent(
            $provider,
            'req-1',
            $model,
            'u-x',
            new \DateTimeImmutable($ts),
            $in,
            $out,
            $cr,
            $cw,
            UsageEvent::STATUS_NEW,
        );
    }

    private function price(Provider $provider, Model $model, string $from, array $rates, string $unit = 'per_1m_tokens'): Price
    {
        $p = new Price($provider, $model, new \DateTimeImmutable($from), 'USD', $unit, $rates);
        // Привязываем к model.rates — для ORM-сбора при rateEvent.
        $model->getPrices()->add($p);

        return $p;
    }

    public function testEvent1(): void
    {
        $provider = new Provider('prism', 'Prism');
        $provider->setOffPeak(16 * 60 + 30, 30, '0.5');
        $model = new Model($provider, 'lumen-8');

        // Цена 2026-08-01, действует до 2026-09-15T12:00.
        $this->price($provider, $model, '2026-08-01T00:00:00Z', [
            'input' => '3.000000', 'output' => '12.000000',
            'cache_read' => '0.300000', 'cache_write' => '3.750000',
        ]);
        // Цена 2026-09-15T12:00 — новая.
        $this->price($provider, $model, '2026-09-15T12:00:00Z', [
            'input' => '1.500000', 'output' => '8.000000',
            'cache_read' => '0.150000', 'cache_write' => '1.875000',
        ]);

        // e1: 11:00 — OLD price (still active), peak (11:00 not off-peak).
        $e1 = $this->event(
            $provider,
            $model,
            '2026-09-15T11:00:00Z',
            1200000,
            800000,
            2500000,
            83333
        );

        $r = (new EventPricingService())->rateEvent($e1);
        self::assertNotNull($r);
        self::assertSame('14.262499', $r['cost']->roundHalfUp6()->toFixed(6));

        // e2: 12:00 — NEW price (valid_from inclusive), peak (12:00 not off-peak).
        $e2 = $this->event(
            $provider,
            $model,
            '2026-09-15T12:00:00Z',
            2000000,
            1000000,
            0,
            0
        );

        $r = (new EventPricingService())->rateEvent($e2);
        self::assertNotNull($r);
        self::assertSame('11.000000', $r['cost']->roundHalfUp6()->toFixed(6));
    }

    public function testEvent3_unified(): void
    {
        $provider = new Provider('sfera', 'Sfera');
        $model = new Model($provider, 'gyro-pro');

        $this->price(
            $provider,
            $model,
            '2026-08-01T00:00:00Z',
            ['any' => '2.400000'],
            Provider::UNIT_UNIFIED
        );

        $e3 = $this->event($provider, $model, '2026-09-20T17:10:00Z', 3000000, 500000);

        $r = (new EventPricingService())->rateEvent($e3);
        self::assertNotNull($r);
        self::assertSame('8.400000', $r['cost']->roundHalfUp6()->toFixed(6));
    }

    public function testEvent_in_offPeak(): void
    {
        $provider = new Provider('prism', 'Prism');
        $provider->setOffPeak(16 * 60 + 30, 30, '0.5');
        $model = new Model($provider, 'lumen-8');

        $this->price(
            $provider,
            $model,
            '2026-08-01T00:00:00Z',
            ['input' => '3.000000', 'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000']
        );

        // off-peak at 17:00
        $e1 = $this->event(
            $provider,
            $model,
            '2026-09-15T17:00:00Z',
            1200000,
            800000,
            2500000,
            83333
        );

        $r = (new EventPricingService())->rateEvent($e1);
        self::assertNotNull($r);
        // 14.26249875 * 0.5 = 7.131249375 → HALF_UP → 7.131249
        self::assertSame('7.131249', $r['cost']->roundHalfUp6()->toFixed(6));
    }

    public function testEvent_noPrice_in_period(): void
    {
        $provider = new Provider('sfera', 'Sfera');
        $model = new Model($provider, 'gyro-pro');
        $this->price(
            $provider,
            $model,
            '2026-10-01T00:00:00Z',
            ['any' => '2.400000'],
            Provider::UNIT_UNIFIED
        );

        // Before 2026-10-01
        $e = $this->event($provider, $model, '2026-09-20T17:10:00Z', 3000000, 500000);
        $r = (new EventPricingService())->rateEvent($e);
        self::assertNull($r);
    }
}
