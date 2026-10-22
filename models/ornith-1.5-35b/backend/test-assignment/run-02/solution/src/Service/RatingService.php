<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Event;
use App\Domain\Entity\Model;
use App\Domain\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Rates usage events at the price valid at their UTC timestamp; when the
 * event's rating date equals the "rating day", recomputes the cost counted in
 * the billing total.
 */
final class RatingService
{
    private EntityRepository $eventRepo;

    public function __construct(
        private PriceIndex $priceIndex,
        private EntityManagerInterface $em,
    ) {
        $this->eventRepo = $this->em->getRepository(Event::class);
    }

    /**
     * Rate every currently unrated event and persist the scores.
     */
    public function rateUnrated(): int
    {
        $events = $this->eventRepo->findBy(['isRated' => false]);
        foreach ($events as $event) {
            $pricing = $this->priceIndex->findPricing($event);
            if (null === $pricing) {
                throw new \RuntimeException('No pricing for '.$event->model->displayName().' at '.$event->timestamp->format(\DateTimeInterface::ATOM));
            }
            $event->isRated = true;
            $event->totalCost = $pricing->cost($event);
        }
        $this->em->persist($events);
        $this->em->flush();

        return count($events);
    }

    /**
     * Reprice all events whose rating date equals $day, scoring them and writing
     * back their cost.
     */
    public function repriceDay(\DateTimeImmutable $day): int
    {
        $events = $this->eventRepo->findBy(['ratingDate' => $day->format('Y-m-d')]);
        foreach ($events as $event) {
            $pricing = $this->priceIndex->findPricing($event);
            if (null === $pricing) {
                throw new \RuntimeException('No pricing for '.$event->model->displayName().' at '.$event->timestamp->format(\DateTimeInterface::ATOM));
            }
            $event->totalCost = $pricing->cost($event);
            $this->em->persist($event);
        }
        $this->em->flush();

        return count($events);
    }
}
