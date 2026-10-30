<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Pricing\Money;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ReportService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UsageEventRepository $events,
    ) {
    }

    /**
     * Возвращает строку CSV (UTF-8, `,`, LF) — готовый stdout.
     */
    public function render(?string $fromIso, ?string $toIso): string
    {
        $from = $fromIso !== null ? new \DateTimeImmutable($fromIso, new \DateTimeZone('UTC')) : null;
        $to = $toIso !== null ? new \DateTimeImmutable($toIso, new \DateTimeZone('UTC')) : null;

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from('App\\Entity\\UsageEvent', 'e')
            ->where('e.status = :st')
            ->setParameter('st', 'rated')
            ->orderBy('e.ts', 'ASC')
            ->orderBy('e.provider', 'ASC')
            ->orderBy('e.providerRequestId', 'ASC');

        if ($from !== null) {
            $qb->andWhere('e.ts >= :f')->setParameter('f', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :t')->setParameter('t', $to);
        }

        $events = $qb->getQuery()->getResult();

        // группировка: (user, provider/model)
        $groups = [];
        $totals = ['in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => new Money('0')];

        foreach ($events as $e) {
            $user = $e->userCode();
            $model = sprintf('%s/%s', $e->provider(), $e->modelCode());
            $key = $user.'|'.$model;

            if (!isset($groups[$key])) {
                $groups[$key] = ['user' => $user, 'model' => $model, 'in' => 0, 'out' => 0, 'cr' => 0, 'cw' => 0, 'cost' => new Money('0')];
            }
            $g = &$groups[$key];
            $g['in'] += $e->tokensInput();
            $g['out'] += $e->tokensOutput();
            $g['cr'] += $e->tokensCacheRead();
            $g['cw'] += $e->tokensCacheWrite();
            $g['cost'] = $g['cost']->add(new Money($e->costUsd() ?? '0'));
            unset($g);
            $totals['in'] += $e->tokensInput();
            $totals['out'] += $e->tokensOutput();
            $totals['cr'] += $e->tokensCacheRead();
            $totals['cw'] += $e->tokensCacheWrite();
            $totals['cost'] = $totals['cost']->add(new Money($e->costUsd() ?? '0'));
        }

        uasort($groups, fn ($a, $b) => strnatcmp($a['user'], $b['user']) === 0
            ? strcmp($a['model'], $b['model'])
            : strnatcmp($a['user'], $b['user']));

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        foreach ($groups as $g) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $this->csv($g['user']),
                $this->csv($g['model']),
                $g['in'],
                $g['out'],
                $g['cr'],
                $g['cw'],
                $g['cost']->format(),
            );
        }
        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totals['in'],
            $totals['out'],
            $totals['cr'],
            $totals['cw'],
            $totals['cost']->format(),
        );

        return implode("\n", $lines) . "\n";
    }

    private function csv(string $s): string
    {
        if ($s === '' || !preg_match('/^[^,]+$/', $s) || str_contains($s, '"')) {
            return '"' . str_replace('"', '""', $s) . '"';
        }
        return $s;
    }
}
