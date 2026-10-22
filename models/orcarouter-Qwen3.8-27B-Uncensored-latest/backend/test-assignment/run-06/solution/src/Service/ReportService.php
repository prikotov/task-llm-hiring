<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\MoneyMath;
use App\Support\IsoTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт расходов по пользователям и моделям.
 *
 * Формат — CSV (UTF-8, `,`, `\n`) строго по спецификации; unrated-события
 * никогда не попадают в отчёт; суммы — по уже округлённым стоимостям
 * событий (повторного округления слагаемых нет).
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws \InvalidArgumentException на некорректных --from/--to
     */
    public function render(string $from, string $to): string
    {
        $fromUtc = $from === '' ? null : IsoTime::parseUtc($from, '--from');
        $toUtc = $to === '' ? null : IsoTime::parseUtc($to, '--to');

        $sql = '
            SELECT
                e.user_id AS user,
                e.provider || \'/\' || e.model AS model,
                SUM(e.tokens_input)        AS tokens_input,
                SUM(e.tokens_output)      AS tokens_output,
                SUM(e.tokens_cache_read)  AS tokens_cache_read,
                SUM(e.tokens_cache_write) AS tokens_cache_write,
                SUM(e.cost_micros)        AS cost_micros
            FROM usage_events e
            WHERE e.status = \'rated\'
        ';
        $params = [];
        $paramTypes = [];
        if ($fromUtc !== null) {
            $sql .= ' AND e.ts >= :from';
            $params['from'] = $fromUtc;
            $paramTypes['from'] = Types::DATETIME_IMMUTABLE;
        }
        if ($toUtc !== null) {
            $sql .= ' AND e.ts < :to';
            $params['to'] = $toUtc;
            $paramTypes['to'] = Types::DATETIME_IMMUTABLE;
        }
        $sql .= '
            GROUP BY e.user_id, e.provider || \'/\' || e.model
            ORDER BY 1 ASC, 2 ASC
        ';

        $rows = $this->em->getConnection()
            ->executeQuery($sql, $params, $paramTypes)
            ->fetchAllAssociative();

        $out = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n";
        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'micros' => 0];
        foreach ($rows as $row) {
            $micros = (int) $row['cost_micros'];
            $out .= sprintf(
                "%s,%s,%d,%d,%d,%d,%s\n",
                $row['user'],
                $row['model'],
                (int) $row['tokens_input'],
                (int) $row['tokens_output'],
                (int) $row['tokens_cache_read'],
                (int) $row['tokens_cache_write'],
                MoneyMath::formatMicros($micros),
            );
            $total['in'] += (int) $row['tokens_input'];
            $total['out'] += (int) $row['tokens_output'];
            $total['cr'] += (int) $row['tokens_cache_read'];
            $total['cw'] += (int) $row['tokens_cache_write'];
            $total['micros'] += $micros;
        }

        $out .= sprintf(
            "TOTAL,,%d,%d,%d,%d,%s\n",
            $total['in'],
            $total['out'],
            $total['cr'],
            $total['cw'],
            MoneyMath::formatMicros($total['micros']),
        );

        return $out;
    }
}
