<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\EventStatus;
use App\Domain\UsageEvent;
use App\Support\Money;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам: группировка рассчитанных событий по (user, model)
 * с суммированием токенов и уже округлённых стоимостей событий
 * (без повторного округления слагаемых).
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Собирает CSV (UTF-8, разделитель ',', перевод строки "\n").
     * Период: от $from включительно, до $to исключительно; null — без ограничения.
     */
    public function buildCsv(?string $fromIso, ?string $toIso): string
    {
        $from = $fromIso === null ? null : \App\Support\IsoTime::parse($fromIso, '--from');
        $to = $toIso === null ? null : \App\Support\IsoTime::parse($toIso, '--to');

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', EventStatus::Rated);
        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        /** @var UsageEvent[] $events */
        $events = $qb->getQuery()->getResult();

        $groups = [];
        foreach ($events as $event) {
            $key = $event->getUser()."|".$event->getProvider().'/'. $event->getModel();
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUser(),
                    'model' => $event->getProvider().'/'. $event->getModel(),
                    'in' => $event->getTokensInput(),
                    'out' => $event->getTokensOutput(),
                    'cr' => $event->getTokensCacheRead(),
                    'cw' => $event->getTokensCacheWrite(),
                    'cost' => $event->getCostUsd() ?? '0',
                ];
            } else {
                $g = &$groups[$key];
                $g['in'] += $event->getTokensInput();
                $g['out'] += $event->getTokensOutput();
                $g['cr'] += $event->getTokensCacheRead();
                $g['cw'] += $event->getTokensCacheWrite();
                $g['cost'] = Money::add($g['cost'], (string) $event->getCostUsd());
                unset($g);
            }
        }

        usort($groups, static fn (array $a, array $b): int => [$a['user'], $a['model']] <=> [$b['user'], $b['model']]);

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        $total = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => '0'];
        foreach ($groups as $g) {
            $lines[] = implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['in'],
                (string) $g['out'],
                (string) $g['cr'],
                (string) $g['cw'],
                Money::format($g['cost'], 6),
            ]);
            $total['in'] += $g['in'];
            $total['out'] += $g['out'];
            $total['cr'] += $g['cr'];
            $total['cw'] += $g['cw'];
            $total['cost'] = Money::add($total['cost'], $g['cost']);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $total['in'],
            (string) $total['out'],
            (string) $total['cr'],
            (string) $total['cw'],
            Money::format($total['cost'], 6),
        ]);

        return implode("\n", $lines). "\n";
    }
}
