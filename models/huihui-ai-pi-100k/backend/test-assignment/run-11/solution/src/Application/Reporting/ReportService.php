<?php

declare(strict_types=1);

namespace App\Application\Reporting;

use App\Domain\Usage\RatedEventEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Формирование CSV-отчёта по рассчитанным событиям.
 *
 * Группировка — по (user, model), отсортировано по (user, model)
 * (лексикографически, по возрастанию); unrated-события в отчёт не
 * попадают (они хранятся в usage_events, но не в rated_events).
 *
 * Формат — строго как в TASK.md (раздел «Команды»):
 *  - заголовок: `user,model,...`
 *  - строки:   `user,provider/code,...`
 *  - TOTAL:    `TOTAL,,...`
 *  - `cost_usd` — ровно 6 знаков после точки.
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Формирует CSV-строки (без финального перевода строки).
     *
     * @return array<int, string>
     */
    public function buildCsv(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array {
        $entities = $this->findRatedInRange($from, $to);

        $groups = [];
        foreach ($entities as $e) {
            $modelInReport = $e->provider . '/' . $e->model;
            $key = $e->user . "\n" . $modelInReport;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $e->user,
                    'model' => $modelInReport,
                    'ti' => 0,
                    'to' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => \App\Domain\Billing\Cost::zero(),
                ];
            }
            $g = &$groups[$key];
            $g['ti'] += $e->tokensInput;
            $g['to'] += $e->tokensOutput;
            $g['cr'] += $e->tokensCacheRead;
            $g['cw'] += $e->tokensCacheWrite;
            $g['cost'] = $g['cost']->add($e->cost());
            unset($g);
        }

        ksort($groups, \SORT_STRING);

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        $totals = ['ti' => 0, 'to' => 0, 'cr' => 0, 'cw' => 0, 'cost' => \App\Domain\Billing\Cost::zero()];

        foreach ($groups as $g) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $g['user'],
                $g['model'],
                $g['ti'],
                $g['to'],
                $g['cr'],
                $g['cw'],
                $g['cost']->toSixDecimalsTruncated(),
            );
            $totals['ti'] += $g['ti'];
            $totals['to'] += $g['to'];
            $totals['cr'] += $g['cr'];
            $totals['cw'] += $g['cw'];
            $totals['cost'] = $totals['cost']->add($g['cost']);
        }

        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totals['ti'],
            $totals['to'],
            $totals['cr'],
            $totals['cw'],
            $totals['cost']->toSixDecimalsTruncated(),
        );

        return $lines;
    }

    /**
     * @return RatedEventEntity[]
     * @internal
     */
    private function findRatedInRange(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(RatedEventEntity::class, 'r');

        if ($from !== null) {
            $qb->andWhere('r.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('r.ts < :to')->setParameter('to', $to);
        }

        return array_values($qb->getQuery()->getResult());
    }
}
