<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Domain\Rater;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use PHPUnit\Framework\TestCase;

final class RaterTest extends TestCase
{
    private int $idSeq = 1;

    private function provider(?int $offStart = null, ?int $offEnd = null, ?string $offMult = null): Provider
    {
        $p = new Provider();
        $p->id = $this->idSeq++;
        $p->code = 'prism';
        $p->name = 'Prism AI';
        $p->offPeakWindowStart = $offStart;
        $p->offPeakWindowEnd = $offEnd;
        $p->offPeakMultiplier = $offMult;

        return $p;
    }

    private function event(Provider $provider, array $tokens = [], string $ts = '2026-09-15T11:00:00Z'): UsageEvent
    {
        $e = new UsageEvent();
        $e->id = $this->idSeq++;
        $e->provider = $provider;
        $e->providerRequestId = 'req-' . $this->idSeq;
        $model = new Model();
        $model->id = $this->idSeq++;
        $model->provider = $provider;
        $model->code = 'lumen-8';
        $e->model = $model;
        $e->user = 'u1';
        $e->occurredAt = new \DateTimeImmutable($ts, new \DateTimeZone('UTC'));
        $e->tokensInput = $tokens['input'] ?? 0;
        $e->tokensOutput = $tokens['output'] ?? 0;
        $e->tokensCacheRead = $tokens['cache_read'] ?? 0;
        $e->tokensCacheWrite = $tokens['cache_write'] ?? 0;

        return $e;
    }

    private function priceRow(array $rates, string $unit): PriceRow
    {
        $p = new PriceRow();
        $p->id = $this->idSeq++;
        $model = new Model();
        $model->id = $this->idSeq++;
        $model->provider = new Provider();
        $p->model = $model;
        $p->validFrom = new \DateTimeImmutable('2026-01-01T00:00:00Z', new \DateTimeZone('UTC'));
        $p->currency = 'USD';
        $p->unit = $unit;
        $p->rates = $rates;

        return $p;
    }

    /**
     * Пример из TASK.md: e1, 1200000/800000/2500000/83333,
     * ставки 3 / 12 / 0.3 / 3.75 за 1M -> ровно 14.262499.
     */
    public function testPerTaskExample(): void
    {
        $rater = new Rater();
        $provider = $this->provider();
        $event = $this->event($provider, [
            'input' => 1200000,
            'output' => 800000,
            'cache_read' => 2500000,
            'cache_write' => 83333,
        ]);
        $row = $this->priceRow(
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            PriceRow::UNIT_PER_TOKEN,
        );
        self::assertSame('14.262499', $rater->rate($event, $row));
    }

    /**
     * Оф-пик: вся стоимость события × multiplier, затем единственное округление:
     * 14.26249875 * 0.5 = 7.131249375 -> 7.131249.
     */
    public function testOffPeakApplied(): void
    {
        $rater = new Rater();
        $provider = $this->provider(0, 0, '0.500000'); // весь день
        $event = $this->event($provider, [
            'input' => 1200000,
            'output' => 800000,
            'cache_read' => 2500000,
            'cache_write' => 83333,
        ]);
        $row = $this->priceRow(
            ['input' => '3.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            PriceRow::UNIT_PER_TOKEN,
        );
        self::assertSame('7.131249', $rater->rate($event, $row));
    }

    /**
     * Схема per_1m_tokens_unified: сумма всех токенов × ставка any / 1M.
     */
    public function testUnified(): void
    {
        $rater = new Rater();
        $provider = $this->provider();
        $event = $this->event($provider, [
            'input' => 1000000,
            'output' => 500000,
            'cache_read' => 0,
            'cache_write' => 0,
        ]);
        $row = $this->priceRow([PriceRow::RATE_UNIFIED => '2.000000'], PriceRow::UNIT_UNIFIED);
        self::assertSame('3.000000', $rater->rate($event, $row));
    }

    public function testUnifiedHalfUpRounding(): void
    {
        $rater = new Rater();
        $provider = $this->provider();
        $event = $this->event($provider, ['input' => 1, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0]);
        // 1 * 0.5 = 0.5 / 1e6 = 0.0000005 -> HALF_UP -> 0.000001
        $row = $this->priceRow([PriceRow::RATE_UNIFIED => '0.500000'], PriceRow::UNIT_UNIFIED);
        self::assertSame('0.000001', $rater->rate($event, $row));
    }

    public function testMissingRateForNonZeroTokens(): void
    {
        $rater = new Rater();
        $provider = $this->provider();
        $event = $this->event($provider, ['input' => 10, 'output' => 5, 'cache_read' => 0, 'cache_write' => 0]);
        $row = $this->priceRow(['input' => '3.000000'], PriceRow::UNIT_PER_TOKEN);
        $this->expectException(DomainException::class);
        $rater->rate($event, $row);
    }
}
