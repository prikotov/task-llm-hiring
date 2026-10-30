<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\MoneyMath;
use App\Repository\UsageEventRepository;

/**
 * Отчёт CSV по расценённым событиям (unrated — никогда).
 * Группировка: (user, model), сортировка lexicographic,
 * суммы стоимостей — без повторного округления.
 */
final class ReportService
{
    public function __construct(private readonly UsageEventRepository $eventRepository)
    {
    }

    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /**
     * @return list<string>
     */
    public function report(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $events = $this->eventRepository->findRated($from, $to);

        $groups = [];
        $totals = [
            'in' => 0,
            'out' => 0,
            'cr' => 0,
            'cw' => 0,
            'cost' => MoneyMath::zero(),
        ];

        foreach ($events as $event) {
            $key = $event->userId . '|' . $event->providerCode . '/' . $event->modelCode;
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->userId,
                    'model' => $event->providerCode . '/' . $event->modelCode,
                    'in' => 0,
                    'out' => 0,
                    'cr' => 0,
                    'cw' => 0,
                    'cost' => MoneyMath::zero(),
                ];
            }
            $groups[$key]['in'] += $event->tokensInput;
            $groups[$key]['out'] += $event->tokensOutput;
            $groups[$key]['cr'] += $event->tokensCacheRead;
            $groups[$key]['cw'] += $event->tokensCacheWrite;
            $groups[$key]['cost'] = MoneyMath::add($groups[$key]['cost'], (string) $event->costUsd);

            $totals['in'] += $event->tokensInput;
            $totals['out'] += $event->tokensOutput;
            $totals['cr'] += $event->tokensCacheRead;
            $totals['cw'] += $event->tokensCacheWrite;
            $totals['cost'] = MoneyMath::add($totals['cost'], (string) $event->costUsd);
        }

        usort($groups, static fn (array $a, array $b): int => $a['user'] <=> $b['user'] ?: $a['model'] <=> $b['model']);

        $rows = [self::HEADER];
        foreach ($groups as $g) {
            $rows[] = implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['in'],
                (string) $g['out'],
                (string) $g['cr'],
                (string) $g['cw'],
                MoneyMath::formatTo6($g['cost']),
            ]);
        }

        $rows[] = 'TOTAL,,' . $totals['in'] . ',' . $totals['out'] . ',' . $totals['cr'] . ',' . $totals['cw']
            . ',' . MoneyMath::formatTo6($totals['cost']);

        return $rows;
    }
}
