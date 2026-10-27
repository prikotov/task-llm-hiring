<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\DecimalMath;

/**
 * Сводный отчёт: рассчитанные события по парам (user, model) в заданном
 * периоде [from, to), с суммированием токенов и стоимостей без повторных
 * округлений (стоимости событий уже округлены до 6 знаков при тарификации).
 */
final class ReportService
{
    public function __construct(private readonly BillingRepository $repo)
    {
    }

    /**
     * @return array{rows: list<array<string, mixed>>, total: array<string, mixed>}
     */
    public function report(\DateTimeImmutable|null $from, \DateTimeImmutable|null $to): array
    {
        $events = $this->repo->findRatedEventsInPeriod($from, $to);

        $groups = [];

        foreach ($events as $event) {
            $user = $event->getUser();
            $model = $event->getProvider()->getCode() . '/' . $event->getModel()->getCode();
            $key = $user . "\x1f" . $model;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost' => '0',
                ];
            }

            $groups[$key]['tokens_input'] += $event->getTokensInput();
            $groups[$key]['tokens_output'] += $event->getTokensOutput();
            $groups[$key]['tokens_cache_read'] += $event->getTokensCacheRead();
            $groups[$key]['tokens_cache_write'] += $event->getTokensCacheWrite();
            $groups[$key]['cost'] = DecimalMath::add($groups[$key]['cost'], $event->getCost() ?? '0');
        }

        $rows = array_values($groups);
        usort($rows, static function (array $a, array $b): int {
            $c = strcmp((string) $a['user'], (string) $b['user']);
            if (0 !== $c) {
                return $c;
            }

            return strcmp((string) $a['model'], (string) $b['model']);
        });

        $total = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost' => '0',
        ];

        foreach ($rows as $row) {
            $total['tokens_input'] += (int) $row['tokens_input'];
            $total['tokens_output'] += (int) $row['tokens_output'];
            $total['tokens_cache_read'] += (int) $row['tokens_cache_read'];
            $total['tokens_cache_write'] += (int) $row['tokens_cache_write'];
            $total['cost'] = DecimalMath::add($total['cost'], (string) $row['cost']);
        }

        return ['rows' => $rows, 'total' => $total];
    }
}
