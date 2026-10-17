<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Агрегация рассчитанных событий по (user, provider/model) за период
 * [$from; $to) (отсутствующая граница — без ограничения).
 *
 * Считаются только события с заполненной стоимостью (unrated — никогда);
 * стоимость группы — сумма уже округлённых до 6 знаков стоимостей событий,
 * повторного округления слагаемых не происходит
 * (Postgres SUM над NUMERIC(30,6) точен).
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return list<ReportRow> отсортировано по user, затем по «provider/model»
     *                         (C-коллиация, по возрастанию)
     */
    public function aggregate(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $sql = <<<'SQL'
            SELECT
                e.username AS "user",
                e.provider_code AS provider,
                e.model_code AS model,
                COALESCE(SUM(e.tokens_input), 0) AS tokens_input,
                COALESCE(SUM(e.tokens_output), 0) AS tokens_output,
                COALESCE(SUM(e.tokens_cache_read), 0) AS tokens_cache_read,
                COALESCE(SUM(e.tokens_cache_write), 0) AS tokens_cache_write,
                COALESCE(SUM(e.cost), '0') AS cost
            FROM usage_events AS e
            WHERE e.cost IS NOT NULL
        SQL;
        $parameters = [];

        if ($from !== null) {
            $sql .= ' AND e.ts >= :from';
            $parameters['from'] = self::toUtcSqlString($from);
        }

        if ($to !== null) {
            $sql .= ' AND e.ts < :to';
            $parameters['to'] = self::toUtcSqlString($to);
        }

        $sql .= <<<'SQL'

            GROUP BY e.username, e.provider_code, e.model_code
            ORDER BY e.username COLLATE "C" ASC, e.provider_code || '/' || e.model_code COLLATE "C" ASC
        SQL;

        $rows = $this->em->getConnection()->fetchAllAssociative($sql, $parameters);

        $result = [];
        foreach ($rows as $row) {
            $result[] = new ReportRow(
                (string) $row['user'],
                (string) $row['provider'],
                (string) $row['model'],
                (int) $row['tokens_input'],
                (int) $row['tokens_output'],
                (int) $row['tokens_cache_read'],
                (int) $row['tokens_cache_write'],
                Money::fromDecimalString((string) $row['cost']),
            );
        }

        return $result;
    }

    /**
     * Момент UTC в явный timestamptz-литерал «YYYY-MM-DD HH:MM:SS+00:00»;
     * PostgreSQL интерпретирует его без зависимости от часовой зоны сессии.
     */
    private static function toUtcSqlString(\DateTimeInterface $value): string
    {
        $utc = $value instanceof \DateTimeImmutable
            ? $value
            : \DateTimeImmutable::createFromImmutable($value);

        $utc = $utc->setTimezone(new \DateTimeZone('UTC'));

        return $utc->format('Y-m-d H:i:s+00:00');
    }
}
