<?php

declare(strict_types=1);

namespace App\Billing;

use App\Support\Decimals;
use Doctrine\DBAL\Connection;

/**
 * Отчёт по расходам (CSV).
 *
 * Группировка — по паре (user, модель «provider/code»), только rated-события;
 * суммы токенов и стоимости — по событиям группы; стоимость группы — сумма
 * уже округлённых стоимостей событий (без повторного округления слагаемых).
 * Период: [from, to), from включительно, to исключительно (по ts событий).
 */
final class ReportService
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /** @var list<array{user:string, model:string, tokensInput:int, tokensOutput:int, tokensCacheRead:int, tokensCacheWrite:int, cost:string}> */
    private array $groups = [];

    private string $totalCost = '0';

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @param \DateTimeInterface|null $from включительно
     * @param \DateTimeInterface|null $to   исключительно
     */
    public function collect(?\DateTimeInterface $from, ?\DateTimeInterface $to): void
    {
        $sql = 'SELECT
                e.user_ref                              AS user_ref,
                e.provider || \'/\' || e.model           AS model,
                SUM(e.tokens_input)                     AS tokens_input,
                SUM(e.tokens_output)                    AS tokens_output,
                SUM(e.tokens_cache_read)                AS tokens_cache_read,
                SUM(e.tokens_cache_write)               AS tokens_cache_write,
                SUM(e.cost_usd::numeric)                AS cost
            FROM usage_events AS e
            WHERE e.status = \'rated\'';
        $params = [];

        if ($from !== null) {
            $sql .= ' AND e.ts >= :from';
            $params['from'] = $this->toUtcString($from);
        }
        if ($to !== null) {
            $sql .= ' AND e.ts < :to';
            $params['to'] = $this->toUtcString($to);
        }

        $sql .= ' GROUP BY e.user_ref, e.provider, e.model
                 ORDER BY e.user_ref COLLATE "C", (e.provider || \'/\' || e.model) COLLATE "C"';

        $rows = $this->connection->fetchAllAssociative($sql, $params);

        $this->groups = [];
        $this->totalCost = '0';
        foreach ($rows as $row) {
            $cost = (string) ($row['cost'] ?? '');
            if ($cost === '') {
                $cost = '0';
            }
            $this->groups[] = [
                'user' => (string) $row['user_ref'],
                'model' => (string) $row['model'],
                'tokensInput' => (int) $row['tokens_input'],
                'tokensOutput' => (int) $row['tokens_output'],
                'tokensCacheRead' => (int) $row['tokens_cache_read'],
                'tokensCacheWrite' => (int) $row['tokens_cache_write'],
                'cost' => $cost,
            ];
            $this->totalCost = Decimals::add($this->totalCost, $cost);
        }
    }

    /**
     * Строки CSV в точном формате ТЗ: заголовок, строки групп, TOTAL.
     *
     * @return list<string>
     */
    public function csvLines(): array
    {
        $lines = [self::HEADER];

        foreach ($this->groups as $g) {
            $lines[] = implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['tokensInput'],
                (string) $g['tokensOutput'],
                (string) $g['tokensCacheRead'],
                (string) $g['tokensCacheWrite'],
                Decimals::format6($g['cost']),
            ]);
        }

        $lines[] = self::summaryLine($this->groups, $this->totalCost);

        return $lines;
    }

    /**
     * @param list<array{tokensInput:int, tokensOutput:int, tokensCacheRead:int, tokensCacheWrite:int}> $groups
     */
    public static function summaryLine(array $groups, string $totalCost): string
    {
        $ti = $to = $cRead = $cWrite = 0;
        foreach ($groups as $g) {
            $ti += $g['tokensInput'];
            $to += $g['tokensOutput'];
            $cRead += $g['tokensCacheRead'];
            $cWrite += $g['tokensCacheWrite'];
        }

        return implode(',', [
            'TOTAL', '', (string) $ti, (string) $to, (string) $cRead, (string) $cWrite,
            Decimals::format6($totalCost),
        ]);
    }

    private function toUtcString(\DateTimeInterface $dt): string
    {
        return $dt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }
}
