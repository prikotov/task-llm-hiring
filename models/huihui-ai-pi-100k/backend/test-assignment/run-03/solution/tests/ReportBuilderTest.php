<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Billing\ReportBuilder;
use App\Domain\Common\Iso8601Utc;
use App\Persistence\Entity\UsageEvent;
use App\Persistence\Repository\UsageEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class ReportBuilderTest extends TestCase
{
    public function testReportGroupsSortsAndTotals(): void
    {
        $events = [
            $this->event('u-bob', 'prism', 'lumen-8', 2000000, 1000000, 0, 0, '11.000000'),
            $this->event('u-alice', 'prism', 'lumen-8', 1200000, 800000, 2500000, 83333, '14.262499'),
            $this->event('u-alice', 'sfera', 'gyro-pro', 3000000, 500000, 0, 0, '8.400000'),
            // Та же группа (u-alice, prism/lumen-8) — суммы складываются.
            $this->event('u-alice', 'prism', 'lumen-8', 100, 200, 0, 0, '0.000001'),
        ];

        $csv = $this->builderFor($events)->build(Iso8601Utc::parse('2026-09-01T00:00:00Z'), Iso8601Utc::parse('2026-10-01T00:00:00Z'));

        $expected = implode("\n", [
            'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd',
            'u-alice,prism/lumen-8,1200100,800200,2500000,83333,14.262500',
            'u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000',
            'u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000',
            'TOTAL,,6200100,2300200,2500000,83333,33.662500',
        ])."\n";

        self::assertSame($expected, $csv);
    }

    public function testEmptySelectionStillPrintsHeaderAndTotal(): void
    {
        $csv = $this->builderFor([])->build(Iso8601Utc::parse('2026-09-01T00:00:00Z'), Iso8601Utc::parse('2026-09-02T00:00:00Z'));

        self::assertSame(
            "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\nTOTAL,,0,0,0,0,0.000000\n",
            $csv,
        );
    }

    public function testCostIsExactSumOfRoundedEventCosts(): void
    {
        // 1.00000049 + 1.00000001 = 2.00000050 → 2.000001 (сложение точных строк,
        // форматирование происходит один раз на выходе).
        $events = [
            $this->event('u-a', 'prism', 'm', 1, 0, 0, 0, '1.000000'),
            $this->event('u-a', 'prism', 'm', 1, 0, 0, 0, '1.000001'),
        ];

        $csv = $this->builderFor($events)->build(Iso8601Utc::parse('2026-09-01T00:00:00Z'), Iso8601Utc::parse('2026-09-02T00:00:00Z'));

        self::assertStringContainsString('u-a,prism/m,2,0,0,0,2.000001', $csv);
        self::assertStringContainsString('TOTAL,,2,0,0,0,2.000001', $csv);
    }

    /**
     * @param list<UsageEvent> $events
     */
    private function builderFor(array $events): ReportBuilder
    {
        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn($events);

        $qb = $this->createMock(QueryBuilder::class);
        foreach (['select', 'from', 'where', 'andWhere', 'setParameter', 'orderBy', 'setMaxResults'] as $method) {
            $qb->method($method)->willReturnSelf();
        }
        $qb->method('getQuery')->willReturn($query);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('createQueryBuilder')->willReturn($qb);

        return new ReportBuilder(new UsageEventRepository($em));
    }

    private function event(string $user, string $provider, string $model, int $in, int $out, int $cr, int $cw, string $cost): UsageEvent
    {
        $e = new UsageEvent();
        $e->provider = $provider;
        $e->providerRequestId = 'req-'.uniqid('', true);
        $e->model = $model;
        $e->user = $user;
        $e->ts = new \DateTimeImmutable('2026-09-15T11:00:00Z');
        $e->tokensInput = $in;
        $e->tokensOutput = $out;
        $e->tokensCacheRead = $cr;
        $e->tokensCacheWrite = $cw;
        $e->billedCost = $cost;

        return $e;
    }
}
