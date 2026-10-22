<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Usage\EventStatus;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт по расходам: CSV, UTF-8, разделитель «,», перевод строки \n.
 *
 * Группировка — (user, provider/model); группы и TOTAL сортированы/собраны
 * по правилам задания. Суммы — сумма уже округлённых стоимостей событий
 * (bcmath, без повторного округления слагаемых). unrated-события не
 * попадают в отчёт никогда. Период — [from, to) по ts события.
 */
final class ReportService
{
    private const TOKEN_COLS = ['tokensInput', 'tokensOutput', 'tokensCacheRead', 'tokensCacheWrite'];

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function buildReport(?string $fromIso, ?string $toIso): string
    {
        $from = $fromIso === null ? null : \App\Shared\IsoUtc::parse($fromIso, '--from');
        $to = $toIso === null ? null : \App\Shared\IsoUtc::parse($toIso, '--to');

        $rows = $this->fetchRatedRows($from, $to);

        /** @var array<string, array{user: string, model: string, in: string, out: string, cr: string, cw: string, cost: string}> $groups */
        $groups = [];
        foreach ($rows as $row) {
            $label = $row['providerCode'].'/'.$row['modelCode'];
            $key = $row['userName']."\x1F".$label;

            if (!isset($groups[$key])) {
                $groups[$key] = ['user' => $row['userName'], 'model' => $label, 'in' => '0', 'out' => '0', 'cr' => '0', 'cw' => '0', 'cost' => '0'];
            }

            $groups[$key]['in'] = bcadd($groups[$key]['in'], (string) $row['tokensInput']);
            $groups[$key]['out'] = bcadd($groups[$key]['out'], (string) $row['tokensOutput']);
            $groups[$key]['cr'] = bcadd($groups[$key]['cr'], (string) $row['tokensCacheRead']);
            $groups[$key]['cw'] = bcadd($groups[$key]['cw'], (string) $row['tokensCacheWrite']);
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], (string) $row['costUsd'], 6);
        }

        uasort($groups, static fn (array $a, array $b): int => strcmp($a['user'], $b['user']) ?: strcmp($a['model'], $b['model']));

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        $total = ['in' => '0', 'out' => '0', 'cr' => '0', 'cw' => '0', 'cost' => '0'];

        foreach ($groups as $group) {
            $lines[] = implode(',', [
                $group['user'],
                $group['model'],
                $group['in'],
                $group['out'],
                $group['cr'],
                $group['cw'],
                $group['cost'],
            ]);
            $total['in'] = bcadd($total['in'], $group['in']);
            $total['out'] = bcadd($total['out'], $group['out']);
            $total['cr'] = bcadd($total['cr'], $group['cr']);
            $total['cw'] = bcadd($total['cw'], $group['cw']);
            $total['cost'] = bcadd($total['cost'], $group['cost'], 6);
        }

        $lines[] = 'TOTAL,,'
            .$total['in'].','.$total['out'].','.$total['cr'].','.$total['cw'].','.$total['cost'];

        return implode("\n", $lines)."\n";
    }

    /**
     * @param \DateTimeImmutable|null $from включительно
     * @param \DateTimeImmutable|null $to  исключительно
     * @return list<array<string, mixed>>
     */
    private function fetchRatedRows(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select(
                'e.userName',
                'e.providerCode',
                'e.modelCode',
                'e.tokensInput',
                'e.tokensOutput',
                'e.tokensCacheRead',
                'e.tokensCacheWrite',
                'e.costUsd',
            )
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', EventStatus::Rated);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}
