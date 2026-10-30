<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Decimal;
use App\Entity\UsageEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам: только рассчитанные события в периоде
 * [from, to); группировка (user, provider/model); суммы — по уже
 * округлённым стоимостям событий.
 */
final class ReportService
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @return string[] строки CSV (включая заголовок и TOTAL) */
    public function build(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $dql = 'SELECT e FROM App\Entity\UsageEvent e WHERE e.status = :status';
        $params = ['status' => UsageEvent::STATUS_RATED];
        if ($from !== null) {
            $dql .= ' AND e.ts >= :from';
            $params['from'] = $from;
        }
        if ($to !== null) {
            $dql .= ' AND e.ts < :to';
            $params['to'] = $to;
        }
        $dql .= ' ORDER BY e.user ASC, e.providerCode ASC, e.modelCode ASC';

        /** @var UsageEvent[] $events */
        $events = $this->em->createQuery($dql)
            ->setParameters($params)
            ->getResult();

        $groups = [];
        foreach ($events as $event) {
            $model = $event->getProviderCode() . '/' . $event->getModelCode();
            $key = $event->getUser() . "\u{1F}" . $model;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUser(),
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
            if ($event->getCostUsd() !== null) {
                $groups[$key]['cost'] = bcadd($groups[$key]['cost'], $event->getCostUsd(), 6);
            }
        }

        $keys = array_keys($groups);
        usort($keys, static function (string $a, string $b): int {
            $pa = explode("\u{1F}", $a, 2);
            $pb = explode("\u{1F}", $b, 2);

            return $pa[0] <=> $pb[0]
                ?: ($pa[1] ?? '') <=> ($pb[1] ?? '');
        });

        $lines = [self::HEADER];
        $total = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0, 'cost' => '0'];
        foreach ($keys as $key) {
            $g = $groups[$key];
            $lines[] = implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['tokens_input'],
                (string) $g['tokens_output'],
                (string) $g['tokens_cache_read'],
                (string) $g['tokens_cache_write'],
                Decimal::format6($g['cost']),
            ]);
            $total['input'] += $g['tokens_input'];
            $total['output'] += $g['tokens_output'];
            $total['cache_read'] += $g['tokens_cache_read'];
            $total['cache_write'] += $g['tokens_cache_write'];
            $total['cost'] = bcadd($total['cost'], $g['cost'], 6);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $total['input'],
            (string) $total['output'],
            (string) $total['cache_read'],
            (string) $total['cache_write'],
            Decimal::format6($total['cost']),
        ]);

        return $lines;
    }
}
