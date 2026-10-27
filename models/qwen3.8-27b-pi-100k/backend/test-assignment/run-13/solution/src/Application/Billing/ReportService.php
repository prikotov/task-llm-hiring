<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money;
use App\Infrastructure\CostRecord;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сводный отчёт по расценённым событиям.
 *
 * Группирует по (user, provider/model), суммирует токены и стоимость
 * (currentCost). Не содержит unrated-событий.
 */
final readonly class ReportService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @return list<array{
     *   user: string,
     *   model: string,
     *   tokens_input: int,
     *   tokens_output: int,
     *   tokens_cache_read: int,
     *   tokens_cache_write: int,
     *   events: int,
     *   cost: string,
     * }>
     */
    public function buildReport(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array {
        $qb = $this->em->createQueryBuilder();
        $qb->select('c.user AS user', 'c.provider AS provider', 'c.model AS model')
            ->addSelect('SUM(c.tokensInput) AS tokens_input')
            ->addSelect('SUM(c.tokensOutput) AS tokens_output')
            ->addSelect('SUM(c.tokensCacheRead) AS tokens_cache_read')
            ->addSelect('SUM(c.tokensCacheWrite) AS tokens_cache_write')
            ->addSelect('COUNT(c.id) AS events')
            ->addSelect('SUM(c.currentCost) AS cost')
            ->from(CostRecord::class, 'c');

        $conditions = [];
        $params = [];
        if ($from !== null) {
            $conditions[] = 'c.ts >= :from';
            $params['from'] = $from;
        }
        if ($to !== null) {
            $conditions[] = 'c.ts < :to';
            $params['to'] = $to;
        }
        if ($conditions !== []) {
            $qb->where(implode(' AND ', $conditions));
            foreach ($params as $key => $value) {
                $qb->setParameter($key, $value);
            }
        }

        $qb->groupBy('c.user', 'c.provider', 'c.model')
            ->orderBy('c.user', 'ASC')
            ->addOrderBy('c.provider', 'ASC')
            ->addOrderBy('c.model', 'ASC');

        /** @var list<array> $rows */
        $rows = $qb->getQuery()->getResult();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'user' => (string) ($row['user'] ?? ''),
                'model' => sprintf('%s/%s', $row['provider'] ?? '', $row['model'] ?? ''),
                'tokens_input' => (int) ($row['tokens_input'] ?? 0),
                'tokens_output' => (int) ($row['tokens_output'] ?? 0),
                'tokens_cache_read' => (int) ($row['tokens_cache_read'] ?? 0),
                'tokens_cache_write' => (int) ($row['tokens_cache_write'] ?? 0),
                'events' => (int) ($row['events'] ?? 0),
                'cost' => Money::round((string) ($row['cost'] ?? '0'), 6),
            ];
        }

        return $result;
    }
}
