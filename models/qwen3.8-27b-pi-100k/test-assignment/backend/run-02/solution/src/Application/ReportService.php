<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\ReportResult;
use App\Application\Dto\ReportRow;
use App\Domain\Billing\Money;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт расходов (app:billing:report).
 *
 * - период: --from включительно, --to исключительно, по ts события;
 * - в отчёт попадают только рассчитанные события (unrated никогда не попадают);
 * - строки — по паре (user, model=<provider>/<code>),
 *   сортировка по user, затем model (лексикографически, по возрастанию);
 * - стоимость группы — сумма уже округлённых стоимостей событий
 *   (повторного округления нет: сумма значений с 6 знаками
 *   снова точна в 6 знаках);
 * - последняя строка — TOTAL с суммами по всему отчёту.
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    public function build(? \DateTimeImmutable $from, ?\DateTimeImmutable $to): ReportResult
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.cost IS NOT NULL');

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        $events = $qb->getQuery()->getResult();

        /** @var array<string, array{user: string, model: string, in: int, out: int, cr: int, cw: int, cost: string}> $groups */
        $groups = [];
        foreach ($events as $event) {
            $key = $event->user()."\0".$event->displayModel();
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->user(),
                    'model' => $event->displayModel(),
                    'in' => 0,
                    'out' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => '0',
                ];
            }
            $groups[$key]['in'] += $event->tokensInput();
            $groups[$key]['out'] += $event->tokensOutput();
            $groups[$key]['cr'] += $event->tokensCacheRead();
            $groups[$key]['cw'] += $event->tokensCacheWrite();
            $groups[$key]['cost'] = Money::add($groups[$key]['cost'], (string) $event->cost());
        }

        $rows = array_values($groups);
        usort($rows, static fn (array $a, array $b): int =>
            $a['user'] <=> $b['user'] ?: $a['model'] <=> $b['model']
        );

        $totals = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0];
        $totalCost = '0';

        $reportRows = [];
        foreach ($rows as $row) {
            $reportRows[] = new ReportRow(
                user: $row['user'],
                model: $row['model'],
                tokensInput: $row['in'],
                tokensOutput: $row['out'],
                tokensCacheRead: $row['cr'],
                tokensCacheWrite: $row['cw'],
                costUsd: Money::format($row['cost']),
            );

            $totals['in'] += $row['in'];
            $totals['out'] += $row['out'];
            $totals['cr'] += $row['cr'];
            $totals['cw'] += $row['cw'];
            $totalCost = Money::add($totalCost, $row['cost']);
        }

        return new ReportResult(
            rows: $reportRows,
            total: new ReportRow(
                user: 'TOTAL',
                model: '',
                tokensInput: $totals['in'],
                tokensOutput: $totals['out'],
                tokensCacheRead: $totals['cr'],
                tokensCacheWrite: $totals['cw'],
                costUsd: Money::format($totalCost),
            ),
        );
    }
}
