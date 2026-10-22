<?php

declare(strict_types=1);

namespace App\Service;

use App\Support\Csv;
use App\Support\Decimal;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

/**
 * Сборка CSV-отчёта по уже рассчитанным событиям за период.
 *
 *  - период: --from включительно, --to исключительно, применяются к ts;
 *  - unrated-события в отчёт не попадают никогда;
 *  - строки — по одной на пару (user, model=<provider>/<code>), сортировка
 *    по user, затем по model (лексикографически, по возрастанию);
 *  - cost_usd — сумма уже округлённых стоимостей событий группы, ровно 6 знаков,
 *    повторного округления слагаемых нет;
 *  - последняя строка — TOTAL (пустое model) с суммами по отчёту;
 *  - вывод: UTF-8, разделитель ",", перевод строки "\n".
 */
final class ReportService
{
    /** Фиксированный заголовок (как в задании). */
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return string полный текст отчёта (завершается символом перевода строки)
     */
    public function generate(?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): string
    {
        $rows = $this->fetchRatedRows($from, $to);

        // агрегация по (user, provider/model)
        $groups = [];
        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = '0';

        foreach ($rows as $row) {
            $key = $row['user_id'] . "\0" . $row['provider'] . '/' . $row['model_code'];
            $ti = (int) $row['tokens_input'];
            $to = (int) $row['tokens_output'];
            $cr = (int) $row['tokens_cache_read'];
            $cw = (int) $row['tokens_cache_write'];
            $cost = (string) $row['cost_usd'];

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $row['user_id'],
                    'model' => $row['provider'] . '/' . $row['model_code'],
                    'ti' => 0,
                    'to' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => '0',
                ];
            }
            $groups[$key]['ti'] += $ti;
            $groups[$key]['to'] += $to;
            $groups[$key]['cr'] += $cr;
            $groups[$key]['cw'] += $cw;
            $groups[$key]['cost'] = Decimal::add($groups[$key]['cost'], $cost);

            $totalInput += $ti;
            $totalOutput += $to;
            $totalCacheRead += $cr;
            $totalCacheWrite += $cw;
            $totalCost = Decimal::add($totalCost, $cost);
        }

        $lines = [self::HEADER];

        usort($groups, static fn (array $a, array $b): int => strcmp($a['user'] . "\0" . $a['model'], $b['user'] . "\0" . $b['model']));

        foreach ($groups as $g) {
            $lines[] = Csv::row([
                (string) $g['user'],
                (string) $g['model'],
                (string) $g['ti'],
                (string) $g['to'],
                (string) $g['cr'],
                (string) $g['cw'],
                Decimal::formatMoney((string) $g['cost']),
            ]);
        }

        $lines[] = Csv::row([
            'TOTAL',
            '',
            (string) $totalInput,
            (string) $totalOutput,
            (string) $totalCacheRead,
            (string) $totalCacheWrite,
            Decimal::formatMoney($totalCost),
        ]);

        return implode("\n", $lines) . "\n";
    }

    public function fetchRatedRows(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $conn = $this->em->getConnection();

        $where = ['status = :status'];
        $params = ['status' => 'rated'];
        if ($from !== null) {
            $where[] = 'ts >= CAST(:from AS timestamptz)';
            $params['from'] = \App\Support\SqlDateTime::utc($from);
        }
        if ($to !== null) {
            $where[] = 'ts < CAST(:to AS timestamptz)';
            $params['to'] = \App\Support\SqlDateTime::utc($to);
        }
        $sql = 'SELECT user_id, provider, model_code, ts, tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, cost_usd'
            . ' FROM usage_events WHERE ' . implode(' AND ', $where) . ' ORDER BY id';

        return $conn->fetchAllAssociative($sql, $params);
    }
}
