<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Domain\UtcDateTime;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам (CSV, UTF-8, разделитель ",", LF).
 *
 * - строки данных — по одной на пару (user, «provider/model»), сортировка по user, затем model (побайтово);
 * - токены — целые суммы по событиям группы;
 * - cost_usd — сумма уже округлённых стоимостей событий (повторного округления слагаемых нет), ровно 6 знаков;
 * - unrated-события не попадают никогда; период — [from, to).
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * @param string|null $from ISO-8601 UTC, включительно (без аргумента — без границы)
     * @param string|null $to   ISO-8601 UTC, исключительно (без аргумента — без границы)
     */
    public function build(?string $from, ?string $to): string
    {
        $fromTs = $from !== null ? UtcDateTime::parse($from, '--from') : null;
        $toTs = $to !== null ? UtcDateTime::parse($to, '--to') : null;

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($fromTs !== null) {
            $qb->andWhere('e.ts >= :reportFrom')->setParameter('reportFrom', $fromTs);
        }
        if ($toTs !== null) {
            $qb->andWhere('e.ts < :reportTo')->setParameter('reportTo', $toTs);
        }

        /** @var list<UsageEvent> $events */
        $events = $qb->getQuery()->getResult();

        $groups = $this->groupEvents($events);

        $lines = [self::HEADER];
        $totals = ['tokens' => [0, 0, 0, 0], 'cost' => '0'];

        foreach ($groups as $g) {
            $lines[] = implode(',', [
                $g['user'],
                $g['model'],
                $g['tokens'][0],
                $g['tokens'][1],
                $g['tokens'][2],
                $g['tokens'][3],
                $g['cost'],
            ]);

            foreach (['in' => 0, 'out' => 1, 'cr' => 2, 'cw' => 3] as $slot) {
                $totals['tokens'][$slot] += $g['tokens'][$slot];
            }
            $totals['cost'] = Money::add($totals['cost'], $g['cost']);
        }

        $totals['cost'] = Money::toScale6($totals['cost']);

        $lines[] = implode(',', [
            'TOTAL',
            '',
            $totals['tokens'][0],
            $totals['tokens'][1],
            $totals['tokens'][2],
            $totals['tokens'][3],
            Money::toScale6($totals['cost']),
        ]);

        return implode("\n", $lines) . "\n";
    }

    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /**
     * @param list<UsageEvent> $events
     *
     * @return list<array{user: string, model: string, tokens: list<int>, cost: string}>
     *         отсортировано по user, затем по model (по возрастанию, побайтово)
     */
    private function groupEvents(array $events): array
    {
        // user => [modelLabel => запись группы]; costRaw хранит точную сумму
        // (сумма значений с ≤ 6 знаками тоже имеет ≤ 6 знаков — округлении не требуется)
        /** @var array<string, array<string, array{user: string, model: string, tokens: list<int>, costRaw: string}>> $tree */
        $tree = [];

        foreach ($events as $e) {
            $label = $e->provider . '/' . $e->model;
            $u = $e->user;

            if (!isset($tree[$u][$label])) {
                $tree[$u][$label] = ['user' => $u, 'model' => $label, 'tokens' => [0, 0, 0, 0], 'costRaw' => '0'];
            }

            $tree[$u][$label]['tokens'][0] += $e->tokensInput;
            $tree[$u][$label]['tokens'][1] += $e->tokensOutput;
            $tree[$u][$label]['tokens'][2] += $e->tokensCacheRead;
            $tree[$u][$label]['tokens'][3] += $e->tokensCacheWrite;
            $tree[$u][$label]['costRaw'] = Money::add($tree[$u][$label]['costRaw'], (string) $e->costUsd);
        }

        $groups = [];
        foreach ($tree as $users) {
            foreach ($users as $cell) {
                $groups[] = [
                    'user' => $cell['user'],
                    'model' => $cell['model'],
                    'tokens' => $cell['tokens'],
                    'cost' => Money::toScale6($cell['costRaw']),
                ];
            }
        }

        usort(
            $groups,
            static fn (array $a, array $b): int => strcmp($a['user'] . "\x1f" . $a['model'], $b['user'] . "\x1f" . $b['model']),
        );

        return $groups;
    }
}
