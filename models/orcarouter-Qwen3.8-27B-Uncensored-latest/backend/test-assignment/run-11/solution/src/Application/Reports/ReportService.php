<?php

declare(strict_types=1);

namespace App\Application\Reports;

use App\Domain\Money;
use App\Domain\UsageEvent;
use App\Repository\DomainRepository;

/**
 * Построение CSV-отчёта по расходам.
 *
 * Период: от --from (включительно) до --to (исключительно).
 * Сортировка: по user, затем по model (лексикографически).
 * unrated-события всегда исключаются.
 */
final class ReportService
{
    public function __construct(
        private readonly DomainRepository $repo,
    ) {
    }

    public function build(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): string {
        $from = $from ?? new \DateTimeImmutable('1970-01-01T00:00:00Z');
        $events = $this->repo->findRatedInRange($from, $to);

        // Группируем по (user, model)
        $groups = [];

        foreach ($events as $event) {
            $user = $event->getUserName();
            $modelKey = $event->getProviderCode().'/'. $event->getModelCode();

            if (!isset($groups[$user.'|'.$modelKey])) {
                $groups[$user.'|'.$modelKey] = [
                    'user' => $user,
                    'model' => $modelKey,
                    'tokens_in' => 0,
                    'tokens_out' => 0,
                    'tokens_cr' => 0,
                    'tokens_cw' => 0,
                    'cost' => Money::zero(),
                ];
            }

            $g = &$groups[$user.'|'.$modelKey];
            $g['tokens_in'] += $event->getTokensInput();
            $g['tokens_out'] += $event->getTokensOutput();
            $g['tokens_cr'] += $event->getTokensCacheRead();
            $g['tokens_cw'] += $event->getTokensCacheWrite();
            $cost = $event->getCost();
            if ($cost !== null) {
                $g['cost'] = $g['cost']->add($cost);
            }
            unset($g);
        }

        // Сортируем по user, затем по model
        uasort($groups, static function (array $a, array $b): int {
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) {
                return $c;
            }

            return strcmp($a['model'], $b['model']);
        });

        // Формируем CSV
        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];

        $totalIn = 0;
        $totalOut = 0;
        $totalCr = 0;
        $totalCw = 0;
        $totalCost = Money::zero();

        foreach ($groups as $g) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $g['user'],
                $g['model'],
                $g['tokens_in'],
                $g['tokens_out'],
                $g['tokens_cr'],
                $g['tokens_cw'],
                $g['cost']->toFixedSix()
            );

            $totalIn += $g['tokens_in'];
            $totalOut += $g['tokens_out'];
            $totalCr += $g['tokens_cr'];
            $totalCw += $g['tokens_cw'];
            $totalCost = $totalCost->add($g['cost']);
        }

        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalIn,
            $totalOut,
            $totalCr,
            $totalCw,
            $totalCost->toFixedSix()
        );

        return implode("\n", $lines);
    }
}
