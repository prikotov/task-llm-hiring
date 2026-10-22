<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\CostCalculator;
use App\Domain\EventStatus;
use App\Domain\PriceRow;
use App\Domain\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий.
 *
 * Каждое событие берёт строку цен своей модели с наибольшим valid_from
 * ≤ ts события (valid_from включительно). Событие, у которой такой строки
 * нет, остаётся unrated; при следующем запуске попытка повторяется —
 * цены могли подтянуться позже.
 */
final class BillingRater
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @return array{rated: int, unrated: list<UsageEvent>} */
    public function ratePending(): array
    {
        /** @var UsageEvent[] $pending */
        $pending = $this->em->createQuery(
            'SELECT e FROM App\Domain\UsageEvent e WHERE e.status = :status ORDER BY e.ts ASC, e.id ASC',
        )
            ->setParameter('status', EventStatus::Unrated)
            ->getResult();

        if ($pending === []) {
            return ['rated' => 0, 'unrated' => []];
        }

        // Для каждой пары (провайдер, модель) держим все строки цен; при выборе
        // — строка с наибольшим valid_from, не превышающим ts события.
        $rowsByKey = [];
        foreach ($this->em->getRepository(PriceRow::class)->findAll() as $row) {
            $key = $row->getProvider()->getCode().'/'.$row->getModel()->getCode();
            $rowsByKey[$key][] = $row;
        }

        $rated = 0;
        $unrated = [];
        foreach ($pending as $event) {
            $row = null;
            foreach ($rowsByKey[$event->getProvider().'/'.$event->getModel()] ?? [] as $candidate) {
                if ($candidate->getValidFrom() <= $event->getTs()) {
                    if ($row === null || $candidate->getValidFrom() > $row->getValidFrom()) {
                        $row = $candidate;
                    }
                }
            }
            if ($row === null) {
                $unrated[] = $event;
                continue;
            }

            $event->markRated(CostCalculator::calculate($row, $event), $row);
            $rated++;
        }

        $this->em->flush();

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
