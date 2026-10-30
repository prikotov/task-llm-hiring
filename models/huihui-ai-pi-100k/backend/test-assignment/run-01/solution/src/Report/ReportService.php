<?php

declare(strict_types=1);

namespace App\Report;

use App\Domain\EventStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам: только рассчитанные (rated) события,
 * отсортировано по user, затем по model (лексикографически, по возрастанию).
 *
 * Период: --from включительно, --to исключительно (по ts события).
 * Суммы строки группы — сумма уже округлённых стоимостей событий;
 * итоговая строка — сумма по отчёту. Повторных округлений нет.
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{
     *     groups: list<array{user: string, model: string, tokens: array<string, string>, cost: string}>,
     *     total: array{tokens: array<string, string>, cost: string},
     * }
     */
    public function report(?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array
    {
        $where = ['u.status = :status'];
        $bind = ['status' => ['value' => EventStatus::Rated->value, 'type' => 'string']];
        if ($from !== null) {
            $where[] = 'u.ts >= :from';
            $bind['from'] = ['value' => $from, 'type' => Types::DATETIMETZ_IMMUTABLE];
        }
        if ($to !== null) {
            $where[] = 'u.ts < :to';
            $bind['to'] = ['value' => $to, 'type' => Types::DATETIMETZ_IMMUTABLE];
        }

        /** @var list<array<string, mixed>> $rows */
        $query = $this->em->createQuery(sprintf(
            <<<DQL
                SELECT
                    u.user AS user,
                    p.code AS providerCode,
                    m.code AS modelCode,
                    SUM(u.tokensInput) AS tokensInput,
                    SUM(u.tokensOutput) AS tokensOutput,
                    SUM(u.tokensCacheRead) AS tokensCacheRead,
                    SUM(u.tokensCacheWrite) AS tokensCacheWrite,
                    SUM(u.cost) AS cost
                FROM App\Entity\UsageEvent AS u
                JOIN u.provider AS p
                JOIN u.model AS m
                WHERE %s
                GROUP BY u.user, p.code, m.code
            DQL,
            implode(' AND ', $where),
        ));

        foreach ($bind as $name => $option) {
            $query->setParameter($name, $option['value'], (string) $option['type']);
        }

        $rows = $query->getArrayResult();

        $tokenKeys = ['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'];

        $groups = [];
        foreach ($rows as $row) {
            $groups[] = [
                'user' => (string) $row['user'],
                'model' => (string) $row['providerCode'] . '/' . (string) $row['modelCode'],
                'tokens' => [
                    'tokens_input' => (string) $row['tokensInput'],
                    'tokens_output' => (string) $row['tokensOutput'],
                    'tokens_cache_read' => (string) $row['tokensCacheRead'],
                    'tokens_cache_write' => (string) $row['tokensCacheWrite'],
                ],
                'cost' => (string) $row['cost'],
            ];
        }

        usort($groups, static fn (array $a, array $b): int => strcmp($a['user'], $b['user']) ?: strcmp($a['model'], $b['model']));

        $total = ['tokens' => ['tokens_input' => '0', 'tokens_output' => '0', 'tokens_cache_read' => '0', 'tokens_cache_write' => '0'], 'cost' => '0'];
        foreach ($groups as $group) {
            foreach ($tokenKeys as $key) {
                $total['tokens'][$key] = bcadd($total['tokens'][$key], $group['tokens'][$key], 6);
            }
            $total['cost'] = bcadd($total['cost'], $group['cost'], 6);
        }

        return ['groups' => $groups, 'total' => $total];
    }
}
