<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use Doctrine\DBAL\Connection;

final class ReportGenerator
{
    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * Generate a CSV report for rated events within the given time range.
     *
     * @param string|null $from ISO-8601 UTC inclusive
     * @param string|null $to ISO-8601 UTC exclusive
     */
    public function generate(?string $from, ?string $to): string
    {
        $sql = "SELECT user_id,
                       provider_code || '/' || model_code AS model,
                       tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
                       cost
                FROM usage_events
                WHERE status = 'rated'";
        $params = [];

        if ($from !== null) {
            $sql .= " AND ts >= ?::timestamptz";
            $params[] = $from;
        }
        if ($to !== null) {
            $sql .= " AND ts < ?::timestamptz";
            $params[] = $to;
        }

        $rows = $this->db->fetchAllAssociative($sql, $params);

        // Group by (user, model)
        $groups = [];
        foreach ($rows as $row) {
            $key = $row['user_id'] . "\x00" . $row['model'];
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $row['user_id'],
                    'model' => $row['model'],
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost' => '0',
                ];
            }
            $groups[$key]['tokens_input'] += (int) $row['tokens_input'];
            $groups[$key]['tokens_output'] += (int) $row['tokens_output'];
            $groups[$key]['tokens_cache_read'] += (int) $row['tokens_cache_read'];
            $groups[$key]['tokens_cache_write'] += (int) $row['tokens_cache_write'];
            $groups[$key]['cost'] = Money::add($groups[$key]['cost'], $row['cost']);
        }

        // Sort by user, then model (lexicographically)
        usort($groups, function (array $a, array $b): int {
            $cmp = strcmp($a['user'], $b['user']);
            if ($cmp !== 0) {
                return $cmp;
            }
            return strcmp($a['model'], $b['model']);
        });

        // Build CSV
        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];

        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = '0';

        foreach ($groups as $g) {
            $costFormatted = Money::format($g['cost'], 6);
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $g['user'],
                $g['model'],
                $g['tokens_input'],
                $g['tokens_output'],
                $g['tokens_cache_read'],
                $g['tokens_cache_write'],
                $costFormatted
            );

            $totalInput += $g['tokens_input'];
            $totalOutput += $g['tokens_output'];
            $totalCacheRead += $g['tokens_cache_read'];
            $totalCacheWrite += $g['tokens_cache_write'];
            $totalCost = Money::add($totalCost, $g['cost']);
        }

        $totalCostFormatted = Money::format($totalCost, 6);
        $lines[] = sprintf('TOTAL,,%d,%d,%d,%d,%s', $totalInput, $totalOutput, $totalCacheRead, $totalCacheWrite, $totalCostFormatted);

        return implode("\n", $lines) . "\n";
    }
}
