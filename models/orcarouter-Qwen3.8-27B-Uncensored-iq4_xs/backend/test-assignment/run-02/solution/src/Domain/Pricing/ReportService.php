<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Common\Money;
use App\Domain\Usage\UsageEventRepository;

use function bcadd;

/**
 * Отчёт по расходам: агрегатация рассчитанных событий по (user, model)
 * за период [from, to) — from включительно, to эксклюзивно.
 *
 * Суммируются уже округлённые стоимости событий, слагаемые не
 * переокругляются.
 */
final class ReportService
{
    public function __construct(private readonly UsageEventRepository $events)
    {
    }

    /** @return string готовый CSV (UTF-8, разделитель ",", "\n", без BOM) */
    public function buildCsv(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        $rows = $this->events->findRatedInRange($from, $to);

        /** @var array<string, array{tokens_input:int, tokens_output:int, tokens_cache_read:int, tokens_cache_write:int, cost:string}> $groups */
        $groups = [];
        $totals = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost' => '0',
        ];

        foreach ($rows as $row) {
            $key = $row['user']."\u{1}".$row['model_label'];
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost' => '0',
                ];
            }

            $groups[$key]['tokens_input'] += $row['tokens_input'];
            $groups[$key]['tokens_output'] += $row['tokens_output'];
            $groups[$key]['tokens_cache_read'] += $row['tokens_cache_read'];
            $groups[$key]['tokens_cache_write'] += $row['tokens_cache_write'];
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], $row['cost'], Money::SCALE);

            $totals['tokens_input'] += $row['tokens_input'];
            $totals['tokens_output'] += $row['tokens_output'];
            $totals['tokens_cache_read'] += $row['tokens_cache_read'];
            $totals['tokens_cache_write'] += $row['tokens_cache_write'];
            $totals['cost'] = bcadd($totals['cost'], $row['cost'], Money::SCALE);
        }

        // Сортировка по user, затем по model (лексикографически, по возрастанию).
        uksort($groups, static fn (string $a, string $b): int => strcmp($a, $b));

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        foreach ($groups as $key => $g) {
            $lines[] = implode(',', [
                explode("\u{1}", $key)[0],
                explode("\u{1}", $key)[1],
                (string) $g['tokens_input'],
                (string) $g['tokens_output'],
                (string) $g['tokens_cache_read'],
                (string) $g['tokens_cache_write'],
                Money::format($g['cost']),
            ]);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $totals['tokens_input'],
            (string) $totals['tokens_output'],
            (string) $totals['tokens_cache_read'],
            (string) $totals['tokens_cache_write'],
            Money::format($totals['cost']),
        ]);

        return implode("\n", $lines)."\n";
    }
}
