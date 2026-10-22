<?php

declare(strict_types=1);

namespace App\Billing\Application\Report;

use App\Billing\Domain\Shared\Decimal;
use App\Billing\Domain\Usage\UsageEvent;
use App\Billing\Infrastructure\Persistence\UsageEventRepository;
use DateTimeImmutable;

/**
 * Агрегирует рассчитанные события в отчёт. Unrated-события не попадают никогда.
 */
final class UsageReportBuilder
{
    public function __construct(private readonly UsageEventRepository $events)
    {
    }

    public function build(?DateTimeImmutable $from, ?DateTimeImmutable $to): UsageReport
    {
        /** @var array<string, array{user: string, model: string, in: int, out: int, cr: int, cw: int, cost: string}> $groups */
        $groups = [];
        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0.000000'];

        foreach ($this->events->findRated($from, $to) as $event) {
            $key = $event->user()."\0".$this->modelLabel($event);
            $groups[$key] ??= [
                'user' => $event->user(),
                'model' => $this->modelLabel($event),
                'in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0,
                'cost' => '0.000000',
            ];
            $this->accumulate($groups[$key], $event);
            $this->accumulate($total, $event);
        }

        uasort($groups, static fn (array $a, array $b): int => [$a['user'], $a['model']] <=> [$b['user'], $b['model']]);

        $rows = array_values(array_map(
            static fn (array $g): UsageReportRow => new UsageReportRow($g['user'], $g['model'], $g['in'], $g['out'], $g['cr'], $g['cw'], $g['cost']),
            $groups,
        ));

        return new UsageReport(
            $rows,
            new UsageReportRow('TOTAL', '', $total['in'], $total['out'], $total['cr'], $total['cw'], $total['cost']),
        );
    }

    private function modelLabel(UsageEvent $event): string
    {
        return $event->provider().'/'.$event->model();
    }

    /** @param array{in: int, out: int, cr: int, cw: int, cost: string} $acc */
    private function accumulate(array &$acc, UsageEvent $event): void
    {
        $usage = $event->usage();
        $acc['in'] += $usage->input;
        $acc['out'] += $usage->output;
        $acc['cr'] += $usage->cacheRead;
        $acc['cw'] += $usage->cacheWrite;
        $acc['cost'] = Decimal::add($acc['cost'], (string) $event->costUsd(), Decimal::MONEY_SCALE);
    }
}
