<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Entity\UsageEvent;

/**
 * Сборка CSV-отчёта (app:billing:report).
 *
 * Правила:
 *  - строки данных — по паре (user, model), отсортированы по user
 *    затем по model;
 *  - `model` — «provider/code» (формат события из импорта);
 *  - токены — сумма по событиям группы (целые);
 *  - `cost_usd` — сумма уже округлённых (на уровне события) стоимостей,
 *    ровно 6 знаков;
 *  - unrated / реплейс-события не попадают никогда;
 *  - период [from, to): включительно / исключительно;
 *  - TOTAL-строка — сумма по отчёту.
 */
final class ReportBuilder
{
    /** Заголовок CSV (строка 0) — ровно то, что ожидает приёмка. */
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /** Запас точности для суммирования. */
    private const SCALE = 16;

    /**
     * @param array<UsageEvent> $events (уже rated + в период)
     * @return array<int, string> строки CSV (включая заголовок и TOTAL)
     */
    public function build(array $events): array
    {
        $groups = [];
        foreach ($events as $e) {
            $key = $e->user.'|'.$e->model;
            if (isset($groups[$key])) {
                $g = $groups[$key];
            } else {
                $g = [
                    'user' => $e->user,
                    'model' => $e->model,
                    'in' => 0,
                    'out' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => '0.000000',
                ];
            }
            $g['in'] += $e->tokensInput;
            $g['out'] += $e->tokensOutput;
            $g['cr'] += $e->tokensCacheRead;
            $g['cw'] += $e->tokensCacheWrite;
            $g['cost'] = bcadd($g['cost'], $e->costUsd, self::SCALE);
            $groups[$key] = $g;
        }

        uasort($groups, static fn (array $a, array $b): int =>
            $a['user'] <=> $b['user'] ?: $a['model'] <=> $b['model']);

        $lines = [];
        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0.000000'];

        foreach ($groups as $g) {
            $cost = bcadd('0', $g['cost'], 6);
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $g['user'],
                $g['model'],
                $g['in'],
                $g['out'],
                $g['cr'],
                $g['cw'],
                $cost,
            );
            $total['in'] += $g['in'];
            $total['out'] += $g['out'];
            $total['cr'] += $g['cr'];
            $total['cw'] += $g['cw'];
            $total['cost'] = bcadd($total['cost'], $cost, self::SCALE);
        }

        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $total['in'],
            $total['out'],
            $total['cr'],
            $total['cw'],
            bcadd('0', $total['cost'], 6),
        );

        return [self::HEADER, ...$lines];
    }
}
