<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Entity\UsageEvent;
use App\Entity\UsageEventStatus;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/** Построение CSV-отчёта сгруппированных расходов. */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return list<list<string>> строки CSV (включая заголовок и TOTAL)
     */
    public function build(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $rows = $this->em->getRepository(UsageEvent::class)->findBy([
            'status' => UsageEventStatus::RATED,
        ], ['ts' => 'ASC']);

        $groups = [];
        foreach ($rows as $event) {
            $ts = $event->getTs();
            if (($from !== null && $ts < $from) || ($to !== null && $ts >= $to)) {
                continue; // период: [from, to)
            }

            $key = $event->getUser()."\x1f".$event->getProvider().'/'.$event->getModel();
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUser(),
                    'model' => $event->getProvider().'/'.$event->getModel(),
                    'tokens' => [0, 0, 0, 0],
                    'cost' => '0.000000',
                ];
            }
            $groups[$key]['tokens'][0] += $event->getTokensInput();
            $groups[$key]['tokens'][1] += $event->getTokensOutput();
            $groups[$key]['tokens'][2] += $event->getTokensCacheRead();
            $groups[$key]['tokens'][3] += $event->getTokensCacheWrite();
            $groups[$key]['cost'] = Money::add($groups[$key]['cost'], (string) $event->getCostUsd());
        }

        uksort($groups, static fn (string $a, string $b): int => strcmp($a, $b));

        $in = $out = $cr = $cw = 0;
        $totalCost = '0.000000';
        $lines = [['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd']];

        foreach ($groups as $g) {
            $tokens = $g['tokens'];
            $in += $tokens[0];
            $out += $tokens[1];
            $cr += $tokens[2];
            $cw += $tokens[3];
            $cost = Money::format6($g['cost']);
            $totalCost = Money::add($totalCost, $g['cost']);
            $lines[] = [
                $g['user'],
                $g['model'],
                (string) $tokens[0],
                (string) $tokens[1],
                (string) $tokens[2],
                (string) $tokens[3],
                $cost,
            ];
        }

        $lines[] = [
            'TOTAL',
            '',
            (string) $in,
            (string) $out,
            (string) $cr,
            (string) $cw,
            Money::format6($totalCost),
        ];

        return $lines;
    }
}
