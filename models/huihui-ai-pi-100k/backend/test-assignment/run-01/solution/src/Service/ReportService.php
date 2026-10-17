<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Decimal;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт (CSV) по рассчитанным событиям за период [from, to).
 */
final class ReportService
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /**
     * Строит отчёт в виде готового CSV (UTF-8, разделитель ",", LF).
     */
    public function build(EntityManagerInterface $em, ?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        $dql = 'SELECT e FROM '.UsageEvent::class.' e WHERE e.status = :status';
        $params = ['status' => UsageEvent::STATUS_RATED];
        if ($from !== null) {
            $dql .= ' AND e.occurredAt >= :from';
            $params['from'] = $from;
        }
        if ($to !== null) {
            $dql .= ' AND e.occurredAt < :to';
            $params['to'] = $to;
        }
        $query = $em->createQuery($dql.' ORDER BY e.id ASC');
        foreach (['status', 'from', 'to'] as $key) {
            if (array_key_exists($key, $params)) {
                $query->setParameter($key, $params[$key]);
            }
        }

        /** @var UsageEvent[] $events */
        $events = $query->getResult();

        /** @var array<string, array{user:string, model:string, in:int, out:int, cr:int, cw:int, cost:string}> $groups */
        $groups = [];
        foreach ($events as $event) {
            $user = $event->user;
            $model = $event->model->displayCode();
            $key = $user."\x00".$model;
            if (!isset($groups[$key])) {
                $groups[$key] = ['user' => $user, 'model' => $model, 'in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0.000000'];
            }
            $groups[$key]['in'] += $event->tokensInput;
            $groups[$key]['out'] += $event->tokensOutput;
            $groups[$key]['cr'] += $event->tokensCacheRead;
            $groups[$key]['cw'] += $event->tokensCacheWrite;
            assert($event->cost !== null);
            $groups[$key]['cost'] = Decimal::addMoney($groups[$key]['cost'], $event->cost);
        }

        uasort($groups, static fn (array $a, array $b): int =>
            $a['user'] <=> $b['user'] ?: $a['model'] <=> $b['model']);

        $lines = [self::HEADER];
        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0.000000'];
        foreach ($groups as $g) {
            $lines[] = implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['in'],
                (string) $g['out'],
                (string) $g['cr'],
                (string) $g['cw'],
                $g['cost'],
            ]);
            $total['in'] += $g['in'];
            $total['out'] += $g['out'];
            $total['cr'] += $g['cr'];
            $total['cw'] += $g['cw'];
            $total['cost'] = Decimal::addMoney($total['cost'], $g['cost']);
        }

        $lines[] = implode(',', ['TOTAL', '', (string) $total['in'], (string) $total['out'], (string) $total['cr'], (string) $total['cw'], $total['cost']]);

        return implode("\n", $lines)."\n";
    }
}
