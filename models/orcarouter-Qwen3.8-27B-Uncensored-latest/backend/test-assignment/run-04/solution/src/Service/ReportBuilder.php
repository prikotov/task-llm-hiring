<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money\Money;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Builds the CSV report over rated events, grouped by (user, model), with the
 * TOTAL row. Token counts are summed; costs are sums of the already-rounded
 * per-event costs (no re-rounding of summands).
 */
final class ReportBuilder
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param \DateTimeImmutable|null $from inclusive
     * @param \DateTimeImmutable|null $to   exclusive
     */
    public function csv(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :rated')
            ->setParameter('rated', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        $groups = [];
        foreach ($qb->getQuery()->getResult() as $item) {
            /** @var UsageEvent $event */
            $event = $item instanceof UsageEvent ? $item : $item[0];

            $key = $event->getUserId() . '|' . $event->getProvider() . '/' . $event->getModel();

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUserId(),
                    'model' => $event->getProvider() . '/' . $event->getModel(),
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
            $groups[$key]['cost'] = Money::add($groups[$key]['cost'], (string) $event->getCost());
        }

        $this->em->clear();

        $total = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost' => '0',
        ];
        $lines = ["user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd"];

        \usort($groups, static fn (array $a, array $b): int => \strcmp($a['user'], $b['user']) ?: \strcmp($a['model'], $b['model']));

        foreach ($groups as $g) {
            $total['tokens_input'] += $g['tokens_input'];
            $total['tokens_output'] += $g['tokens_output'];
            $total['tokens_cache_read'] += $g['tokens_cache_read'];
            $total['tokens_cache_write'] += $g['tokens_cache_write'];
            $total['cost'] = Money::add($total['cost'], $g['cost']);

            $lines[] = \implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['tokens_input'],
                (string) $g['tokens_output'],
                (string) $g['tokens_cache_read'],
                (string) $g['tokens_cache_write'],
                Money::format6($g['cost']),
            ]);
        }

        $lines[] = \implode(',', [
            'TOTAL',
            '',
            (string) $total['tokens_input'],
            (string) $total['tokens_output'],
            (string) $total['tokens_cache_read'],
            (string) $total['tokens_cache_write'],
            Money::format6($total['cost']),
        ]);

        return \implode("\n", $lines) . "\n";
    }
}
