<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\BillingService;
use App\Domain\Model\Model;
use App\Domain\Model\PriceLine;
use App\Domain\Provider\Provider;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class CostTest extends TestCase
{
    private function service(): BillingService
    {
        $em = $this->createEmptyEm();

        return new BillingService($em);
    }

    private function createEmptyEm(): EntityManagerInterface&MockObject
    {
        $qb = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('createQueryBuilder')->willReturn($qb);

        return $em;
    }

    private function event(int $in, int $out, int $cr, int $cw, string $ts): UsageEvent
    {
        $e = new UsageEvent();
        $e->setProvider('prism');
        $e->setProviderRequestId('x');
        $e->setModel('lumen-8');
        $e->setUser('u');
        $e->setTokens($in, $out, $cr, $cw);
        $e->setTs(new \DateTimeImmutable($ts, new \DateTimeZone('UTC')));

        return $e;
    }

    private function line(string $unit, array $rates): PriceLine
    {
        $provider = new Provider();
        $model = new Model();
        $model->setProvider($provider);

        $line = new PriceLine();
        $line->setProvider($provider);
        $line->setModel($model);
        $line->setUnit($unit);
        $line->setRatesJson(json_encode($rates));
        $line->setValidFrom(new \DateTimeImmutable('2026-08-01T00:00:00Z', new \DateTimeZone('UTC')));

        return $line;
    }

    public function testPerTokenCostExampleE1(): void
    {
        $line = $this->line(PriceLine::UNIT_TOKENS, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        $provider = new Provider();
        $provider->setOffPeak(null, null, null);

        $cost = $this->service()->computeCost(
            $this->event(1200000, 800000, 2500000, 83333, '2026-09-15T11:00:00Z'),
            $line,
            $provider,
        );

        self::assertSame('14.262499', $cost);
    }

    public function testUnifiedCostExampleE3(): void
    {
        $line = $this->line(PriceLine::UNIT_UNIFIED, ['any' => '2.400000']);
        $provider = new Provider();
        $provider->setOffPeak(null, null, null);

        $cost = $this->service()->computeCost(
            $this->event(3000000, 500000, 0, 0, '2026-09-20T17:10:00Z'),
            $line,
            $provider,
        );

        self::assertSame('8.400000', $cost);
    }

    public function testOffPeakMultiplierAppliedToOneEvent(): void
    {
        $line = $this->line(PriceLine::UNIT_TOKENS, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);
        $provider = new Provider();
        $provider->setOffPeak('16:30', '00:30', '0.5');

        // 17:00 — внутри окна; 1M input x 3 = 3.0, x0.5 = 1.5
        $cost = $this->service()->computeCost(
            $this->event(1000000, 0, 0, 0, '2026-09-15T17:00:00Z'),
            $line,
            $provider,
        );

        self::assertSame('1.500000', $cost);
    }

    public function testZeroTokens(): void
    {
        $line = $this->line(PriceLine::UNIT_UNIFIED, ['any' => '2.400000']);
        $provider = new Provider();
        $provider->setOffPeak(null, null, null);

        $cost = $this->service()->computeCost(
            $this->event(0, 0, 0, 0, '2026-09-15T17:00:00Z'),
            $line,
            $provider,
        );

        self::assertSame('0.000000', $cost);
    }
}
