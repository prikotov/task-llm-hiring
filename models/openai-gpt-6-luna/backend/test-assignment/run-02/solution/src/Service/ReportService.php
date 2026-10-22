<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UsageEvent;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(?DateTimeImmutable $from, ?DateTimeImmutable $to): string
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select(
                'e.userId AS user_id',
                'e.providerCode AS provider_code',
                'e.modelCode AS model_code',
                'e.tokensInput AS tokens_input',
                'e.tokensOutput AS tokens_output',
                'e.tokensCacheRead AS tokens_cache_read',
                'e.tokensCacheWrite AS tokens_cache_write',
                'e.costUsd AS cost_usd',
            )
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');
        if ($from !== null) {
            $query->andWhere('e.occurredAt >= :from')->setParameter('from', InputData::sqlTimestamp($from), Types::STRING);
        }
        if ($to !== null) {
            $query->andWhere('e.occurredAt < :to')->setParameter('to', InputData::sqlTimestamp($to), Types::STRING);
        }
        $rows = $query->getQuery()->getScalarResult();
        $groups = [];
        $totalTokens = ['0', '0', '0', '0'];
        $totalCost = '0.000000';
        foreach ($rows as $row) {
            $key = json_encode([(string) $row['user_id'], (string) $row['provider_code'], (string) $row['model_code']], JSON_THROW_ON_ERROR);
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => (string) $row['user_id'],
                    'model' => (string) $row['provider_code'].'/'.(string) $row['model_code'],
                    'tokens' => ['0', '0', '0', '0'],
                    'cost' => '0.000000',
                ];
            }
            foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $index => $field) {
                $value = (string) $row[$field];
                $groups[$key]['tokens'][$index] = bcadd($groups[$key]['tokens'][$index], $value, 0);
                $totalTokens[$index] = bcadd($totalTokens[$index], $value, 0);
            }
            $cost = (string) $row['cost_usd'];
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], $cost, 6);
            $totalCost = bcadd($totalCost, $cost, 6);
        }

        $groups = array_values($groups);
        usort($groups, static fn (array $left, array $right): int => strcmp($left['user'], $right['user']) ?: strcmp($left['model'], $right['model']));

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        foreach ($groups as $group) {
            $lines[] = $this->csvRow([
                $group['user'], $group['model'], ...$group['tokens'], $group['cost'],
            ]);
        }
        $lines[] = $this->csvRow(['TOTAL', '', ...$totalTokens, $totalCost]);

        return implode("\n", $lines)."\n";
    }

    /** @param list<string> $fields */
    private function csvRow(array $fields): string
    {
        return implode(',', array_map(static function (string $field): string {
            if (strpbrk($field, ",\"\r\n") === false) {
                return $field;
            }
            return '"'.str_replace('"', '""', $field).'"';
        }, $fields));
    }
}
