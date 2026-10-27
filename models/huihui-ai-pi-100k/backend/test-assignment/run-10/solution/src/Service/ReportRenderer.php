<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money\Decimal;
use App\Repository\UsageEventRepository;

/**
 * Собирает CSV-отчёт (app:billing:report).
 *
 * - строки — по (user, model), отсортированы по user, затем model;
 * - cost = сумма УЖЕ ОКРУГЛЁННЫХ стоимостей событий (правило 4:
 *   слагаемые не перекругляются, только представлено в 6 знаков);
 * - unrated-события в отчёт не попадают;
 * - последняя строка — TOTAL.
 */
final class ReportRenderer
{
    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private UsageEventRepository $events)
    {
    }

    public function render(\DateTimeImmutable|null $from, \DateTimeImmutable|null $to): string
    {
        $groups = [];
        $order = [];

        foreach ($this->events->findRatedBetween($from, $to) as $event) {
            $user = $event->user();
            $model = $event->modelName();
            $key = $user . "\x00" . $model;

            if (!isset($groups[$key])) {
                $groups[$key] = ['user' => $user, 'model' => $model, 'in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];
                $order[] = $key;
            }

            $counts = $event->counts();
            $groups[$key]['in'] += $counts->input;
            $groups[$key]['out'] += $counts->output;
            $groups[$key]['cr'] += $counts->cacheRead;
            $groups[$key]['cw'] += $counts->cacheWrite;
            $groups[$key]['cost'] = Decimal::add($groups[$key]['cost'], (string) ($event->cost() ?? '0'));
        }

        usort($order, fn (string $a, string $b): int => $this->cmpGroup($a, $b));

        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];
        $lines = [self::HEADER];

        foreach ($order as $key) {
            $g = $groups[$key];

            $lines[] = implode(',', [
                $this->csv($g['user']),
                $this->csv($g['model']),
                (string) $g['in'],
                (string) $g['out'],
                (string) $g['cr'],
                (string) $g['cw'],
                Decimal::toSixDecimals($g['cost']),
            ]);

            $total['in'] += $g['in'];
            $total['out'] += $g['out'];
            $total['cr'] += $g['cr'];
            $total['cw'] += $g['cw'];
            $total['cost'] = Decimal::add($total['cost'], $g['cost']);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $total['in'],
            (string) $total['out'],
            (string) $total['cr'],
            (string) $total['cw'],
            Decimal::toSixDecimals($total['cost']),
        ]);

        return implode("\n", $lines) . "\n";
    }

    private function cmpGroup(string $a, string $b): int
    {
        [$ua, $ma] = explode("\x00", $a, 2);
        [$ub, $mb] = explode("\x00", $b, 2);

        $c = strcmp($ua, $ub);
        if ($c !== 0) {
            return $c;
        }

        return strcmp($ma, $mb);
    }

    private function csv(string $value): string
    {
        if (strpbrk($value, ",\"\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }
}
