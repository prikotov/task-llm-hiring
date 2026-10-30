<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Billing\BillingReportBuilder;
use App\Domain\Catalog\Model;
use App\Domain\Usage\BillingStatus;
use App\Domain\Usage\UsageEvent;
use App\Infrastructure\Persistence\UsageEventRepository;
use PHPUnit\Framework\TestCase;

final class BillingReportBuilderTest extends TestCase
{
    private function repository(array $events): UsageEventRepository
    {
        return new class($events) extends UsageEventRepository {
            public function __construct(private readonly array $events)
            {
                // Parent constructor (ManagerRegistry) не нужен для юнит-теста.
            }

            public function findRatedInPeriod(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
            {
                $result = [];
                foreach ($this->events as $event) {
                    if ($from !== null && $event->ts() < $from) {
                        continue;
                    }
                    if ($to !== null && $event->ts() >= $to) {
                        continue;
                    }
                    $result[] = $event;
                }

                return $result;
            }
        };
    }

    private function event(string $id, string $provider, string $modelCode, string $user, string $ts, string $cost): UsageEvent
    {
        $event = new UsageEvent(
            $provider,
            $id,
            new Model($provider, $modelCode),
            $user,
            new \DateTimeImmutable($ts),
            100,
            200,
            0,
            0,
        );
        $event->rateWith(
            new \App\Domain\Catalog\PriceRow(
                new Model($provider, $modelCode),
                new \DateTimeImmutable('2026-08-01T00:00:00+00:00'),
                'USD',
                \App\Domain\Catalog\PricingUnit::Per1MTokens,
                ['input' => '1', 'output' => '1', 'cache_read' => '0.1', 'cache_write' => '1'],
            ),
            \App\Domain\Money\Money::exact($cost),
        );

        return $event;
    }

    public function testGroupsSortAndTotals(): void
    {
        $events = [
            $this->event('e3', 'prism', 'lumen-8', 'u-bob', '2026-09-15T12:00:00+00:00', '11.000000'),
            $this->event('e2', 'sfera', 'gyro-pro', 'u-alice', '2026-09-20T17:10:00+00:00', '8.400000'),
            $this->event('e1', 'prism', 'lumen-8', 'u-alice', '2026-09-15T11:00:00+00:00', '14.262499'),
        ];

        $report = (new BillingReportBuilder($this->repository($events)))->build(null, null);

        $keys = array_map(
            static fn ($row) => $row->user.'|'.$row->model,
            $report->rows,
        );
        self::assertSame(
            ['u-alice|prism/lumen-8', 'u-alice|sfera/gyro-pro', 'u-bob|prism/lumen-8'],
            $keys,
        );
        self::assertSame('14.262499', $report->rows[0]->costUsd);
        self::assertSame('8.400000', $report->rows[1]->costUsd);
        self::assertSame([100, 200, 0, 0], $report->rows[0]->tokens);
        self::assertSame('33.662499', $report->totalCostUsd);
        self::assertSame([300, 600, 0, 0], $report->totalTokens);
    }

    public function testUnratedEventsAreExcludedByRepositoryContract(): void
    {
        // Не рассчитанное событие repo (findRatedInPeriod) не должен возвращать;
        // проверяем, что билдер не трогает статус и не падает на rated-наборе.
        $rated = $this->event('e1', 'prism', 'lumen-8', 'u-alice', '2026-09-15T11:00:00+00:00', '1.000000');
        $unrated = new UsageEvent(
            'prism',
            'e2',
            new Model('prism', 'lumen-8'),
            'u-alice',
            new \DateTimeImmutable('2026-09-15T12:00:00+00:00'),
            1,
            1,
            0,
            0,
        );

        self::assertTrue($rated->isRated());
        self::assertSame(BillingStatus::Unrated, $unrated->status());

        $report = (new BillingReportBuilder($this->repository([$rated])))->build(null, null);
        self::assertCount(1, $report->rows);
    }

    public function testPeriodFilterFromInclusiveToExclusive(): void
    {
        $inWindow = $this->event('e1', 'prism', 'lumen-8', 'u-alice', '2026-09-15T00:00:00+00:00', '1.000000');
        $before = $this->event('e2', 'prism', 'lumen-8', 'u-alice', '2026-09-14T23:59:59+00:00', '2.000000');
        $atTo = $this->event('e3', 'prism', 'lumen-8', 'u-alice', '2026-10-01T00:00:00+00:00', '3.000000');

        $report = (new BillingReportBuilder($this->repository([$inWindow, $before, $atTo])))->build(
            new \DateTimeImmutable('2026-09-15T00:00:00+00:00'),
            new \DateTimeImmutable('2026-10-01T00:00:00+00:00'),
        );

        self::assertCount(1, $report->rows);
        self::assertSame('1.000000', $report->rows[0]->costUsd);
        self::assertSame('1.000000', $report->totalCostUsd);
    }
}
