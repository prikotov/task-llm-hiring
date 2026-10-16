<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Decimal;
use App\Domain\Rater;
use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий.
 *
 * Выбор цены: строка модели с наибольшим valid_from <= ts (valid_from включительно).
 * Не найдена → событие остаётся unrated (повторная попытка на следующем запуске).
 */
final class RatingService
{
    public function __construct(
        private readonly Rater $rater,
    ) {
    }

    /**
     * @return RatingResult
     */
    public function rateUnrated(EntityManagerInterface $em): RatingResult
    {
        /** @var UsageEvent[] $events */
        $events = $em->createQuery(
            'SELECT e FROM '.UsageEvent::class.' e WHERE e.status = :status ORDER BY e.id ASC'
        )
            ->setParameter('status', UsageEvent::STATUS_UNRATED)
            ->getResult();

        $rated = 0;
        $priceRowsByModel = [];
        $unrated = [];

        foreach ($events as $event) {
            $modelId = $event->model->id;
            if (!isset($priceRowsByModel[$modelId])) {
                $priceRowsByModel[$modelId] = $this->priceRowsForModel($em, $modelId);
            }

            $row = $this->selectApplicable($priceRowsByModel[$modelId], $event->occurredAt);
            if ($row === null) {
                $unrated[] = $event;
                continue;
            }

            $event->status = UsageEvent::STATUS_RATED;
            $event->priceRow = $row;
            $event->cost = $this->rater->rate($event, $row);
            $event->ratedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            $rated++;
        }

        $em->flush();

        return new RatingResult($rated, $unrated);
    }

    /**
     * @param PriceRow[] $rows
     */
    private function selectApplicable(array $rows, \DateTimeImmutable $ts): ?PriceRow
    {
        $best = null;
        foreach ($rows as $row) {
            if ($row->validFrom > $ts) {
                continue;
            }
            if ($best === null || $row->validFrom > $best->validFrom) {
                $best = $row;
            }
        }

        return $best;
    }

    /**
     * @return PriceRow[]
     */
    private function priceRowsForModel(EntityManagerInterface $em, int $modelId): array
    {
        return $em->createQuery(
            'SELECT pr FROM '.PriceRow::class.' pr JOIN pr.model m WHERE m.id = :mid ORDER BY pr.validFrom DESC'
        )
            ->setParameter('mid', $modelId)
            ->getResult();
    }
}

/**
 * @readonly
 */
final class RatingResult
{
    /**
     * @param UsageEvent[] $unrated
     */
    public function __construct(
        public readonly int $rated,
        public readonly array $unrated,
    ) {
    }
}
