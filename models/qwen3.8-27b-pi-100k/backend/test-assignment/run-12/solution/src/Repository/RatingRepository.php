<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class RatingRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** Создаёт или обновляет rated-строку (unique по event_id). Возвращает стоимость (bcmath строка). */
    public function markRated(int $eventId, int $priceRowId, string $cost): void
    {
        $r = $this->em->getRepository(Rating::class)->findOneBy(['eventId' => $eventId]);
        if ($r !== null) {
            $r->priceRowId = $priceRowId;
            $r->cost = $cost;
            $r->ratedAt = new DateTimeImmutable();
            return;
        }

        $r = new Rating();
        $r->eventId = $eventId;
        $r->priceRowId = $priceRowId;
        $r->cost = $cost;
        $r->ratedAt = new DateTimeImmutable();
        $this->em->persist($r);
    }

    /** @return int[] event_id'ы, у которых уже есть rated-строка */
    public function ratedEventIds(): array
    {
        $rows = $this->em->createQuery('SELECT r.eventId FROM App\Entity\Rating r')->getResult();

        return array_map('intval', $rows);
    }
}
