<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Decimal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;

/**
 * Отчёт о расходах (app:billing:report): группировка рассчитанных событий
 * по (user, <provider>/<model>), период [from, to).
 *
 * cost_usd группы = сумма уже округлённых (до 6 знаков) стоимостей событий,
 * без повторного округления слагаемых (точное десятичное сложение).
 */
final class ReportService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @throws DbalException
     *
     * @return array{
     *   rows: array<array{user: string, model: string, tokens: array<string,int>, cost: string}>,
     *   total_tokens: array<string,int>,
     *   total_cost: string
     * }
     */
    public function report(?string $from, ?string $to): array
    {
        $where = "e.status = 'rated'";
        $params = [];
        if ($from !== null) {
            $where .= ' AND e.ts >= :from';
            $params['from'] = $from;
        }
        if ($to !== null) {
            $where .= ' AND e.ts < :to';
            $params['to'] = $to;
        }

        $rows = $this->connection->fetchAllAssociative(
            "SELECT e.user_id AS user,
                    e.provider_code || '/' || e.model_code AS model,
                    SUM(e.tokens_input) AS tokens_input,
                    SUM(e.tokens_output) AS tokens_output,
                    SUM(e.tokens_cache_read) AS tokens_cache_read,
                    SUM(e.tokens_cache_write) AS tokens_cache_write,
                    SUM(e.cost) AS cost
             FROM usage_events e
             WHERE $where
             GROUP BY e.user_id, e.provider_code, e.model_code
             ORDER BY e.user_id ASC, e.provider_code || '/' || e.model_code ASC",
            $params,
        );

        $result = [];
        $total = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0];
        $totalCost = '0';

        foreach ($rows as $r) {
            $cost = (string) $r['cost'];
            $result[] = [
                'user' => (string) $r['user'],
                'model' => (string) $r['model'],
                'tokens' => [
                    'input' => (int) $r['tokens_input'],
                    'output' => (int) $r['tokens_output'],
                    'cache_read' => (int) $r['tokens_cache_read'],
                    'cache_write' => (int) $r['tokens_cache_write'],
                ],
                'cost' => $cost,
            ];

            foreach ($result[\count($result) - 1]['tokens'] as $kind => $v) {
                $total[$kind] += $v;
            }
            $totalCost = Decimal::add($totalCost, $cost);
        }

        return [
            'rows' => $result,
            'total_tokens' => $total,
            'total_cost' => $totalCost,
        ];
    }
}
