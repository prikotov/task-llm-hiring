<?php

namespace App\Service;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class ReportService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function generate(string $from = null, string $to = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');

        if ($from) {
            $queryBuilder->andWhere('e.ts >= :from')
                ->setParameter('from', new \DateTimeImmutable($from));
        }
        if ($to) {
            $queryBuilder->andWhere('e.ts < :to')
                ->setParameter('to', new \DateTimeImmutable($to));
        }

        $events = $queryBuilder->getQuery()->getResult();
        
        $groups = [];
        foreach ($events as $event) {
            $modelStr = (string)$event->model; 
            // If model is not linked, should we show it? The report wants <provider>/<code>.
            // If model is not linked, we might not be able to resolve provider.
            // But the rated events MUST have a model linked because they were rated.
            
            $key = $event->userName . '|' . $modelStr;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->userName,
                    'model' => $modelStr,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }
            
            $groups[$key]['tokens_input'] += $event->tokensInput;
            $groups[$key]['tokens_output'] += $event->tokensOutput;
            $groups[$key]['tokens_cache_read'] += $event->tokensCacheRead;
            $groups[$key]['tokens_cache_write'] += $event->tokensCacheWrite;
            $groups[$key]['cost_usd'] = bcadd($groups[$key]['cost_usd'], $event->costUsd, 6);
        }

        // Sort by user, then model
        uasort($groups, function ($a, $b) {
            $userCmp = strcmp($a['user'], $b['user']);
            if ($userCmp !== 0) return $userCmp;
            return strcmp($a['model'], $b['model']);
        });

        return $groups;
    }
}
