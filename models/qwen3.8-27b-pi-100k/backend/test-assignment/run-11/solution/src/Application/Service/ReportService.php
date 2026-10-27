<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Money;
use App\Entity\UsageEvent;
use App\Entity\UsageRating;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт: строки по (user, model) для событий из периода, + TOTAL в конце.
 * В отчёт попадают только тарифицированные события
 * (cost_usd не null), отсортированные по user затем model.
 * Суммы — бегущие по уже округлённым стоимостям событий (Money).
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param \DateTimeImmutable|null $from включительно
     * @param \DateTimeImmutable|null $to   исключительно
     *
     * @return array{
     *   rows: array<array{user:string,model:string,ti:int,to_:int,cr:int,cw:int,cost:Money}>,
     *   total: array{ti:int,to_:int,cr:int,cw:int,cost:Money}
     * }
     */
    public function report(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $q = $this->em->createQueryBuilder();
        $q->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');
        if ($from !== null) {
            $q->andWhere('e.ts >= :from')
             ->setParameter('from', $from);
        }
        if ($to !== null) {
            $q->andWhere('e.ts < :to')
             ->setParameter('to', $to);
        }
        $q->orderBy('e.ts', 'ASC');
        $events = $q->getQuery()->getResult();

        // Для каждого события берём последние строки рейтинга.
        // Так как rate/reprice-журналируют в usage_ratings с последним состоянием,
        // текущая стоимость = last(id) по event.
        $eventIds = array_map(static fn ($e) => $e->id(), $events);
        $lastRatings = $this->fetchLastRatingsFor($eventIds);

        $rows = [];
        $totTi = 0; $totTo = 0; $totCr = 0; $totCw = 0;
        $totCost = Money::zero();

        foreach ($events as $ev) {
            /** @var UsageEvent $ev */
            $rating = $lastRatings[$ev->id() ?? 0] ?? null;
            if ($rating === null) {
                continue; // no current rating (unrated)
            }
            $modelKey = $ev->provider() . '/' . $ev->model();
            $groupKey = $ev->user() . '|' . $modelKey;

            if (!isset($rows[$groupKey])) {
                $rows[$groupKey] = [
                    'user' => $ev->user(),
                    'model' => $modelKey,
                    'ti' => 0, 'to_' => 0, 'cr' => 0, 'cw' => 0,
                    'cost' => Money::zero(),
                ];
            }
            $rows[$groupKey]['ti'] += $ev->tokensInput();
            $rows[$groupKey]['to_'] += $ev->tokensOutput();
            $rows[$groupKey]['cr'] += $ev->tokensCacheRead();
            $rows[$groupKey]['cw'] += $ev->tokensCacheWrite();
            $rows[$groupKey]['cost'] = $rows[$groupKey]['cost']->add(Money::of($rating->amount()));

            $totTi += $ev->tokensInput();
            $totTo += $ev->tokensOutput();
            $totCr += $ev->tokensCacheRead();
            $totCw += $ev->tokensCacheWrite();
            $totCost = $totCost->add(Money::of($rating->amount()));
        }

        // сортировка: user, затем model (обя обсортировка — по возрастанию, без учёта регистра)
        usort($rows, static function (array $a, array $b): int {
            $r = strcmp($a['user'], $b['user']);
            if ($r !== 0) {
                return $r;
            }
            return strcmp($a['model'], $b['model']);
        });

        return [
            'rows' => array_values($rows),
            'total' => [
                'ti' => $totTi,
                'to_' => $totTo,
                'cr' => $totCr,
                'cw' => $totCw,
                'cost' => $totCost,
            ],
        ];
    }

    /**
     * @param array<int|int> $eventIds
     * @return array<int,UsageRating> event_id => latest UsageRating
     */
    private function fetchLastRatingsFor(array $eventIds): array
    {
        if ($eventIds === []) {
            return [];
        }
        // max(id) по каждому event — последнее состояние стоимости
        $rows = $this->em->getConnection()->fetchAllAssociative(
            'SELECT id, event_id FROM usage_ratings WHERE event_id IN (' . implode(',', array_map('intval', $eventIds)) . ')'
        );
        if ($rows === []) {
            return [];
        }
        // group by event: keep max id
        $lastIdByEvent = [];
        foreach ($rows as $row) {
            $eid = (int) $row['event_id'];
            $id = (int) $row['id'];
            if (!isset($lastIdByEvent[$eid]) || $id > $lastIdByEvent[$eid]) {
                $lastIdByEvent[$eid] = $id;
            }
        }
        $ids = array_values($lastIdByEvent);
        $ratings = $this->em->getRepository(UsageRating::class)->findAll();
        $byId = [];
        foreach ($ratings as $r) {
            $byId[$r->id()] = $r;
        }
        $result = [];
        foreach ($lastIdByEvent as $eid => $rid) {
            if (isset($byId[$rid])) {
                $result[$eid] = $byId[$rid];
            }
        }
        return $result;
    }
}
