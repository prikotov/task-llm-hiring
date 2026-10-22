<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Billing\Money;
use App\Domain\Usage\EventStatus;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сводный отчёт: по одной строке на (user, model), модель — "provider/code".
 * Суммы — по событиям группы; стоимость — сумма уже округлённых
 * стоимостей событий (без повторного округления слагаемых).
 * unrated-события в отчёт не попадают никогда.
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * Период: from включительно, to исключительно (применяется к ts событий).
     *
     * @return array{
     *   rows: list<array{user:string, model:string, ti:int, to:int, cr:int, cw:int, cost:string}>,
     *   total: array{ti:int, to:int, cr:int, cw:int, cost:string}
     * }
     */
    public function report(?string $from, ?string $to): array
    {
        $where = "e.status = '".EventStatus::Rated->value."'";
        $params = [];

        if ($from !== null) {
            $where .= ' AND e.ts >= :from';
            $params['from'] = (new \DateTimeImmutable($from))->format('Y-m-d\TH:i:s\Z');
        }
        if ($to !== null) {
            $where .= ' AND e.ts < :to';
            $params['to'] = (new \DateTimeImmutable($to))->format('Y-m-d\TH:i:s\Z');
        }

        $result = $this->em->getConnection()->fetchAllAssociative(
            'SELECT e.user_id AS "user", e.provider AS provider, e.model_code AS "model",
                    SUM(e.tokens_input) AS tokens_input,
                    SUM(e.tokens_output) AS tokens_output,
                    SUM(e.tokens_cache_read) AS tokens_cache_read,
                    SUM(e.tokens_cache_write) AS tokens_cache_write,
                    SUM(e.cost_usd) AS cost
             FROM usage_events e
             WHERE '.$where.'
             GROUP BY e.user_id, e.provider, e.model_code
             ORDER BY e.user_id ASC, e.provider ASC, e.model_code ASC',
            $params,
        );

        $rows = [];
        $total = ['ti' => 0, 'to' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];
        foreach ($result as $r) {
            $cost = Money::format6((string) $r['cost']);
            $rows[] = [
                'user' => (string) $r['user'],
                'model' => (string) $r['provider'].'/'.(string) $r['model'],
                'ti' => (int) $r['tokens_input'],
                'to' => (int) $r['tokens_output'],
                'cr' => (int) $r['tokens_cache_read'],
                'cw' => (int) $r['tokens_cache_write'],
                'cost' => $cost,
            ];

            $total['ti'] += (int) $r['tokens_input'];
            $total['to'] += (int) $r['tokens_output'];
            $total['cr'] += (int) $r['tokens_cache_read'];
            $total['cw'] += (int) $r['tokens_cache_write'];
            $total['cost'] = Money::add($total['cost'], $cost);
        }

        $total['cost'] = Money::format6($total['cost']);
        return ['rows' => $rows, 'total' => $total];
    }
}
