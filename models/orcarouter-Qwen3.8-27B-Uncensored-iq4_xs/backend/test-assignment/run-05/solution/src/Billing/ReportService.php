<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Usage\UsageEvent;
use App\Domain\ValueObject\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам (CSV): группировка рассчитанных событий за период
 * [from, to) по (user, model), суммы токенов и стоимостей, строка TOTAL.
 *
 * Стоимости суммируются bcmath по уже округлённым (6 знаков) стоимости
 * событий — без повторного округления слагаемых.
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Строки CSV (без BOM, "\n" в конце каждой строки, включая последнюю).
     */
    public function buildReport(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        $rows = $this->fetchEvents($from, $to);

        /** @var array<string, array{input: int, output: int, cacheRead: int, cacheWrite: int, cost: string}> $groups */
        $groups = [];
        foreach ($rows as $row) {
            $key = $row['user']."\x00".$row['provider'].'/'.$row['model'];
            if (!isset($groups[$key])) {
                $groups[$key] = ['input' => 0, 'output' => 0, 'cacheRead' => 0, 'cacheWrite' => 0, 'cost' => '0'];
            }
            $groups[$key]['input'] += $row['tokens_input'];
            $groups[$key]['output'] += $row['tokens_output'];
            $groups[$key]['cacheRead'] += $row['tokens_cache_read'];
            $groups[$key]['cacheWrite'] += $row['tokens_cache_write'];
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], $row['cost'], Money::FINAL_SCALE);
        }

        ksort($groups, SORT_STRING);

        $out = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n";
        $total = ['input' => 0, 'output' => 0, 'cacheRead' => 0, 'cacheWrite' => 0, 'cost' => '0'];
        foreach ($groups as $key => $g) {
            $pos = strpos($key, "\x00");
            $user = substr($key, 0, $pos);
            $model = substr($key, $pos + 1);
            $out .= sprintf(
                "%s,%s,%d,%d,%d,%d,%s\n",
                $user,
                $model,
                $g['input'],
                $g['output'],
                $g['cacheRead'],
                $g['cacheWrite'],
                $g['cost'],
            );
            $total['input'] += $g['input'];
            $total['output'] += $g['output'];
            $total['cacheRead'] += $g['cacheRead'];
            $total['cacheWrite'] += $g['cacheWrite'];
            $total['cost'] = bcadd($total['cost'], $g['cost'], Money::FINAL_SCALE);
        }

        $out .= sprintf(
            "TOTAL,,%d,%d,%d,%d,%s\n",
            $total['input'],
            $total['output'],
            $total['cacheRead'],
            $total['cacheWrite'],
            $total['cost'],
        );

        return $out;
    }

    /**
     * @return list<array{user: string, provider: string, model: string, tokens_input: int, tokens_output: int, tokens_cache_read: int, tokens_cache_write: int, cost: string}>
     */
    private function fetchEvents(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $sql = 'SELECT "user", provider, model, tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, cost'
             .' FROM usage_events WHERE rated = TRUE AND cost IS NOT NULL';
        $parameters = [];
        $types = [];

        if ($from !== null) {
            $sql .= ' AND ts >= :from';
            $parameters['from'] = $from;
            $types['from'] = Types::DATETIME_IMMUTABLE;
        }
        if ($to !== null) {
            $sql .= ' AND ts < :to';
            $parameters['to'] = $to;
            $types['to'] = Types::DATETIME_IMMUTABLE;
        }

        $rows = $this->em->getConnection()->executeQuery($sql, $parameters, $types)->fetchAllAssociative();

        foreach ($rows as $i => $row) {
            $rows[$i]['tokens_input'] = (int) $row['tokens_input'];
            $rows[$i]['tokens_output'] = (int) $row['tokens_output'];
            $rows[$i]['tokens_cache_read'] = (int) $row['tokens_cache_read'];
            $rows[$i]['tokens_cache_write'] = (int) $row['tokens_cache_write'];
            $rows[$i]['cost'] = (string) $row['cost'];
        }

        return $rows;
    }
}
