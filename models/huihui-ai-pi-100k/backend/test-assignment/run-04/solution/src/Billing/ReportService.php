<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\MoneyMath;
use Doctrine\DBAL\Connection;

/** Построение CSV-отчёта по рассчитанным событиям (unrated-события не попадают). */
final class ReportService
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(
        private readonly Connection $db,
    ) {
    }

    public function build(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        $sql = 'SELECT user_id, provider_code, model_code,
                       tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, cost_usd
                FROM usage_events
                WHERE status = \'rated\'';
        $params = [];

        if ($from !== null) {
            $sql .= ' AND ts >= ?';
            $params[] = $from->format('Y-m-d H:i:s');
        }
        if ($to !== null) {
            $sql .= ' AND ts < ?';
            $params[] = $to->format('Y-m-d H:i:s');
        }

        $groups = [];
        foreach ($this->db->fetchAllAssociative($sql, $params) as $row) {
            $key = $row['user_id']."\u{1}".$row['provider_code'].'/'.$row['model_code'];
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $row['user_id'],
                    'model' => $row['provider_code'].'/'.$row['model_code'],
                    'in' => 0,
                    'out' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => '0',
                ];
            }
            $groups[$key]['in'] += (int) $row['tokens_input'];
            $groups[$key]['out'] += (int) $row['tokens_output'];
            $groups[$key]['cr'] += (int) $row['tokens_cache_read'];
            $groups[$key]['cw'] += (int) $row['tokens_cache_write'];
            // Суммы отчёта — сумма уже округлённых стоимостей событий (без повторного округления).
            $groups[$key]['cost'] = MoneyMath::add($groups[$key]['cost'], (string) $row['cost_usd']);
        }

        ksort($groups); // user, затем model — лексикографически по возрастанию

        $lines = [self::HEADER];
        $totals = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];

        foreach ($groups as $g) {
            $lines[] = implode(',', [
                $this->field($g['user']),
                $this->field($g['model']),
                (string) $g['in'],
                (string) $g['out'],
                (string) $g['cr'],
                (string) $g['cw'],
                MoneyMath::format6($g['cost']),
            ]);
            $totals['in'] += $g['in'];
            $totals['out'] += $g['out'];
            $totals['cr'] += $g['cr'];
            $totals['cw'] += $g['cw'];
            $totals['cost'] = MoneyMath::add($totals['cost'], $g['cost']);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $totals['in'],
            (string) $totals['out'],
            (string) $totals['cr'],
            (string) $totals['cw'],
            MoneyMath::format6($totals['cost']),
        ]);

        return implode("\n", $lines)."\n";
    }

    private function field(string $value): string
    {
        if (preg_match('/["\r\n,]/', $value) === 1) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
