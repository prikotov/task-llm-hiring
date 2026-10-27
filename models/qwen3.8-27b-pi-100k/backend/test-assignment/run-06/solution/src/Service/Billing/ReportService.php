<?php

declare(strict_types=1);

namespace App\Service\Billing;

use App\Domain\Billing\PricingCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сводный отчёт по рассчитанным событиям: группировка по (user, model),
 * суммы целых токенов и суммы уже округлённых (до 6 знаков) стоимостей
 * событий — без повторного округления слагаемых.
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @return array{
     *   rows: list<array{user: string, model: string, tokens_input: int, tokens_output: int,
     *                    tokens_cache_read: int, tokens_cache_write: int, cost: string}>,
     *   total: array{tokens_input: int, tokens_output: int, tokens_cache_read: int,
     *                tokens_cache_write: int, cost: string}
     * }
     */
    public function build(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $where = ["e.status = 'rated'", 'e.cost_usd IS NOT NULL'];
        $params = [];

        if ($from !== null) {
            $where[] = 'e.ts >= :from';
            $params['from'] = $from->format('Y-m-d H:i:s');
        }
        if ($to !== null) {
            $where[] = 'e.ts < :to';
            $params['to'] = $to->format('Y-m-d H:i:s');
        }

        $sql = 'SELECT e.user_login AS user,
                       e.provider || \'/\' || e.model AS model,
                       SUM(e.tokens_input) AS ti,
                       SUM(e.tokens_output) AS to_,
                       SUM(e.tokens_cache_read) AS tcr,
                       SUM(e.tokens_cache_write) AS tcw,
                       SUM(e.cost_usd::numeric(24, 6)) AS cost
                FROM usage_events e
                WHERE ' . implode(' AND ', $where) . "
                GROUP BY e.user_login, e.provider, e.model
                ORDER BY e.user_login ASC, e.provider ASC, e.model ASC";

        $rows = $this->em->getConnection()->fetchAllAssociative($sql, $params);

        $result = [];
        $total = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost' => '0',
        ];

        foreach ($rows as $row) {
            $cost = PricingCalculator::format6((string) $row['cost']); // уже 6 знаков, нормализация
            $result[] = [
                'user' => (string) $row['user'],
                'model' => (string) $row['model'],
                'tokens_input' => (int) $row['ti'],
                'tokens_output' => (int) $row['to_'],
                'tokens_cache_read' => (int) $row['tcr'],
                'tokens_cache_write' => (int) $row['tcw'],
                'cost' => $cost,
            ];

            $total['tokens_input'] += (int) $row['ti'];
            $total['tokens_output'] += (int) $row['to_'];
            $total['tokens_cache_read'] += (int) $row['tcr'];
            $total['tokens_cache_write'] += (int) $row['tcw'];
            $total['cost'] = bcadd($total['cost'], $cost, 6);
        }

        return ['rows' => $result, 'total' => $total];
    }
}
