<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\BillingReport;
use App\Entity\UsageEvent;
use App\Shared\Math\Decimal;
use Doctrine\ORM\EntityManagerInterface;

/**
 * app:billing:report — агрегация рассчитанных событий за период
 * [[--from, --to): from включительно, to исключительно; по ts события.
 *
 * Группировка — по (user, provider/code). Суммы токенов — целые;
 * cost_usd — сумма уже округлённых стоимостей событий (без повторного
 * округления слагаемых). Отсортировано по user, затем по модели
 * (лексикографически, по возрастанию). Unrated-события никогда не попадают
 * в отчёт.
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function report(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): BillingReport
    {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        /** @var UsageEvent[] $events */
        $events = $qb->getQuery()->getResult();

        $groups = [];
        $totalTokens = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0];
        $totalCost = '0';

        foreach ($events as $event) {
            $model = $event->getModel();
            $label = $model->getProvider()->getCode().'/'.$model->getCode();
            $key = $event->getUser()."\x00".$label;
            $tokens = $event->tokens();

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUser(),
                    'model' => $label,
                    'tokens' => ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0],
                    'cost' => '0',
                ];
            }

            foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
                $groups[$key]['tokens'][$kind] += $tokens[$kind];
                $totalTokens[$kind] += $tokens[$kind];
            }

            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], (string) $event->getCostUsd(), 6);
            $totalCost = bcadd($totalCost, (string) $event->getCostUsd(), 6);
        }

        $rows = array_values($groups);
        foreach ($rows as &$row) {
            $row['cost'] = Decimal::money6($row['cost']);
        }
        unset($row);

        usort($rows, static fn (array $a, array $b): int =>
            strcmp($a['user'], $b['user']) ?: strcmp($a['model'], $b['model']));

        return new BillingReport($rows, $totalTokens, Decimal::money6($totalCost));
    }
}
