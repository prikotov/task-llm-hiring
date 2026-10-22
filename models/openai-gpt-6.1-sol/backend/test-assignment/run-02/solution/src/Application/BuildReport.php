<?php

declare(strict_types=1);

namespace App\Application;

use Doctrine\DBAL\Connection;

final class BuildReport
{
    public const HEADER = ['user', 'model', 'tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write', 'cost_usd'];

    public function __construct(private Connection $connection)
    {
    }

    /** @return \Generator<list<string>> */
    public function rows(?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): \Generator
    {
        if ($from !== null && $to !== null && $from > $to) {
            throw new \InvalidArgumentException('--from не может быть позже --to.');
        }
        $where = ['cost_usd IS NOT NULL'];
        $parameters = [];
        if ($from !== null) {
            $where[] = 'occurred_at >= :from';
            $parameters['from'] = $from->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
        }
        if ($to !== null) {
            $where[] = 'occurred_at < :to';
            $parameters['to'] = $to->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
        }
        // NUMERIC/SUM в PostgreSQL — точная арифметика; bigint-суммы не переполняют PHP int.
        $sql = <<<'SQL'
            SELECT user_id, provider_code || '/' || model_code AS model_key,
                   SUM(tokens_input)::text, SUM(tokens_output)::text,
                   SUM(tokens_cache_read)::text, SUM(tokens_cache_write)::text,
                   SUM(cost_usd)::text
            FROM billing_usage
            WHERE %s
            GROUP BY user_id, model_key
            ORDER BY user_id COLLATE "C", (provider_code || '/' || model_code) COLLATE "C"
            SQL;
        $result = $this->connection->executeQuery(sprintf($sql, implode(' AND ', $where)), $parameters);
        $totals = ['0', '0', '0', '0', '0.000000'];
        try {
            yield self::HEADER;
            while (($row = $result->fetchNumeric()) !== false) {
                $row = array_map(strval(...), $row);
                for ($i = 0; $i < 5; ++$i) {
                    $totals[$i] = bcadd($totals[$i], $row[$i + 2], $i === 4 ? 6 : 0);
                }
                yield $row;
            }
            yield ['TOTAL', '', ...$totals];
        } finally {
            $result->free();
        }
    }
}
