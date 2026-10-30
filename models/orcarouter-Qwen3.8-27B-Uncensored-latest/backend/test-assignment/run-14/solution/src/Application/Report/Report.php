<?php

declare(strict_types=1);

namespace App\Application\Report;

use App\Shared\Money\Dec;

/**
 * Report generation on top of rated usage events (TASK rule 6):
 * per-user × per-model totals, then the "all / all" row, then the
 * all-users cost-share percentages (HALF_UP to 6 dp).
 */
final class Report
{
    /**
     * @param list<array<string, mixed>> $rows rated events, ts in [fromIso, toIso)
     * @return array{
     *     rows: array<int, array{user: string, model: string, costUsd: string, exactCostUsd: string, costSharePct: string}>,
     *     totals: array{costUsd: string, exactCostUsd: string},
     * }
     */
    public static function build(array $rows, ?string $fromIso = null, ?string $toIso = null): array
    {
        // group by (user, model)
        $cells = [];
        $byUser = [];
        $totalExact = '0';
        $totalMoney = '0';

        foreach ($rows as $r) {
            $user = (string) $r['user'];
            $model = (string) $r['model'];
            $exact = (string) $r['exact'];
            $money = (string) $r['cost'];

            $key = $user . "\x1f" . $model;
            if (!isset($cells[$key])) {
                $cells[$key] = ['user' => $user, 'model' => $model, 'exact' => '0', 'money' => '0'];
            }
            $cells[$key]['exact'] = bcadd($cells[$key]['exact'], $exact, Dec::SCALE);
            $cells[$key]['money'] = bcadd($cells[$key]['money'], $money, Dec::SCALE);

            $byUser[$user] = bcadd((string) ($byUser[$user] ?? '0'), $money, Dec::SCALE);

            $totalExact = bcadd($totalExact, $exact, Dec::SCALE);
            $totalMoney = bcadd($totalMoney, $money, Dec::SCALE);
        }

        $totalMoneyMoney = Dec::money($totalMoney);

        $out = [];
        foreach ($cells as $cell) {
            $out[] = [
                'user' => $cell['user'],
                'model' => $cell['model'],
                'exactCostUsd' => Dec::exact($cell['exact']),
                'costUsd' => Dec::money($cell['money']),
                'costSharePct' => self::share($cell['money'], $totalMoney, $totalMoneyMoney),
            ];
        }
        // stable order: user, then model
        usort($out, static fn (array $a, array $b): int => $a['user'] <=> $b['user'] ?: $a['model'] <=> $b['model']);
        // append totals row
        $out[] = [
            'user' => 'all',
            'model' => 'all',
            'exactCostUsd' => Dec::exact($totalExact),
            'costUsd' => $totalMoneyMoney,
            'costSharePct' => '1.000000',
        ];

        return [
            'rows' => $out,
            'totals' => [
                'costUsd' => $totalMoneyMoney,
                'exactCostUsd' => Dec::exact($totalExact),
            ],
        ];
    }

    /**
     * Share of a user in total cost, as a string:
     * pct = user / total * 100, HALF_UP to 6 decimals.
     *
     * @param string $userExact plain decimal
     * @param string $totalExact plain decimal
     * @param string $totalMoney the rounded total (used only for the zero guard)
     */
    public static function share(string $userExact, string $totalExact, string $totalMoney = '0'): string
    {
        if (bccomp($totalExact, '0', Dec::SCALE) === 0 && $totalMoney === '0.000000') {
            return '0.000000';
        }
        // user / total * 100 at SCALE, then round
        $pct = bcdiv($userExact, $totalExact, Dec::SCALE);
        $pct = bcmul($pct, '100', Dec::SCALE);
        return Dec::money($pct);
    }
}
