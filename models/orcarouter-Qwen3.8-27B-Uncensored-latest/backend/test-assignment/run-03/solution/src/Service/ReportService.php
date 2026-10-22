<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use DateTimeImmutable;

/**
 * Сборка CSV-отчёта по рассчитанным событиям за период.
 *
 * Выход:
 *   - строки данных — по одной на пару (user, model), отсортированы по
 *     user затем по model (лексикографически);
 *   - количества токенов — суммы по событиям группы (целые);
 *   - cost_usd — сумма округлённых стоимостей событий группы (6 знаков);
 *   - последняя строка — TOTAL.
 */
final class ReportService
{
    public function __construct(private readonly UsageEventRepository $events)
    {
    }

    /**
     * @return array{
     *   rows: array<int, array{user: string, model: string, tokens: array<int, int>, cost: string}>,
     *   total: array{input: string, output: string, cache_read: string, cache_write: string, cost: string}
     * }
     */
    public function report(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        /** @var UsageEvent[] $events */
        $events = $this->events->findRatedInRange($from, $to);

        /** @var array<string, array<string, array{tokens: array<int, int>, cost: string}>> $groups */
        $groups = [];

        foreach ($events as $event) {
            $user = $event->getUser();
            $model = $event->getProvider() . '/' . ltrim($event->getModel(), '/');

            if (!isset($groups[$user][$model])) {
                $groups[$user][$model] = ['tokens' => [0, 0, 0, 0], 'cost' => '0'];
            }

            $groups[$user][$model]['tokens'][0] += $event->getTokensInput();
            $groups[$user][$model]['tokens'][1] += $event->getTokensOutput();
            $groups[$user][$model]['tokens'][2] += $event->getTokensCacheRead();
            $groups[$user][$model]['tokens'][3] += $event->getTokensCacheWrite();
            $groups[$user][$model]['cost'] = Money::add($groups[$user][$model]['cost'], $event->getCost() ?? '0');
        }

        $rows = [];
        $grand = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0, 'cost' => '0'];

        foreach ($groups as $user => $models) {
            foreach ($models as $model => $agg) {
                $rows[] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens' => $agg['tokens'],
                    'cost' => Money::format($agg['cost'], 6),
                ];

                $grand['input'] += $agg['tokens'][0];
                $grand['output'] += $agg['tokens'][1];
                $grand['cache_read'] += $agg['tokens'][2];
                $grand['cache_write'] += $agg['tokens'][3];
                $grand['cost'] = Money::add($grand['cost'], $agg['cost']);
            }
        }

        usort($rows, static function (array $a, array $b): int {
            $cmp = strcmp($a['user'], $b['user']);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp($a['model'], $b['model']);
        });

        return [
            'rows' => $rows,
            'total' => [
                'input' => (string) $grand['input'],
                'output' => (string) $grand['output'],
                'cache_read' => (string) $grand['cache_read'],
                'cache_write' => (string) $grand['cache_write'],
                'cost' => Money::format($grand['cost'], 6),
            ],
        ];
    }
}
