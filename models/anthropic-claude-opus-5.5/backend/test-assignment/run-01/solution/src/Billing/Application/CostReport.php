<?php

declare(strict_types=1);

namespace App\Billing\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;

/**
 * Отчёт расходов по (user, model). Суммирование — в PostgreSQL над NUMERIC
 * (точная арифметика), значения возвращаются строками.
 */
final class CostReport
{
    public function __construct(private readonly Connection $db)
    {
    }

    /** @return array{rows: list<ReportRow>, total: ReportRow} */
    public function build(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $where = ['rated_at IS NOT NULL'];
        $params = [];
        if ($from !== null) {
            $where[] = 'ts >= ?';
            $params[] = $from->format('Y-m-d H:i:s.uP');
        }
        if ($to !== null) {
            $where[] = 'ts < ?';
            $params[] = $to->format('Y-m-d H:i:s.uP');
        }

        $sql = "SELECT user_id, provider_code || '/' || model_code AS model,"
            .' SUM(tokens_input)::text AS ti, SUM(tokens_output)::text AS tout,'
            .' SUM(tokens_cache_read)::text AS tcr, SUM(tokens_cache_write)::text AS tcw,'
            .' SUM(cost_usd)::numeric(30,6)::text AS cost'
            .' FROM usage_event WHERE '.implode(' AND ', $where)
            .' GROUP BY user_id, provider_code, model_code';

        $data = $this->db->fetchAllAssociative($sql, $params);
        // байтовая лексикографическая сортировка, не зависящая от collation БД
        usort($data, static fn (array $a, array $b): int => strcmp((string) $a['user_id'], (string) $b['user_id'])
            ?: strcmp((string) $a['model'], (string) $b['model']));

        $rows = [];
        $sum = ['0', '0', '0', '0', '0'];
        foreach ($data as $r) {
            $row = new ReportRow((string) $r['user_id'], (string) $r['model'], (string) $r['ti'], (string) $r['tout'], (string) $r['tcr'], (string) $r['tcw'], (string) $r['cost']);
            $rows[] = $row;
            $sum = [
                bcadd($sum[0], $row->tokensInput),
                bcadd($sum[1], $row->tokensOutput),
                bcadd($sum[2], $row->tokensCacheRead),
                bcadd($sum[3], $row->tokensCacheWrite),
                bcadd($sum[4], $row->costUsd, 6),
            ];
        }

        return ['rows' => $rows, 'total' => new ReportRow('TOTAL', '', $sum[0], $sum[1], $sum[2], $sum[3], bcadd($sum[4], '0', 6))];
    }
}
