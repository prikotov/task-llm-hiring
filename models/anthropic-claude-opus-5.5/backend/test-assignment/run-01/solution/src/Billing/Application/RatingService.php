<?php

declare(strict_types=1);

namespace App\Billing\Application;

use App\Pricing\Infrastructure\PricingRepository;
use App\Usage\Domain\UsageEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий (включая повторную попытку для unrated).
 */
final class RatingService
{
    private const int BATCH = 500;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricingRepository $pricing,
    ) {
    }

    /**
     * @param callable(UsageEvent): void $onUnrated
     * @return array{rated: int, unrated: int}
     */
    public function rateAll(callable $onUnrated): array
    {
        $rated = 0;
        $unrated = 0;
        $lastId = 0;
        $now = new DateTimeImmutable();

        while (true) {
            /** @var list<UsageEvent> $batch */
            $batch = $this->em->createQueryBuilder()
                ->select('e')->from(UsageEvent::class, 'e')
                ->where('e.ratedAt IS NULL')->andWhere('e.id > :last')
                ->setParameter('last', $lastId)
                ->orderBy('e.id')->setMaxResults(self::BATCH)
                ->getQuery()->getResult();
            if ($batch === []) {
                break;
            }

            $this->em->wrapInTransaction(function () use ($batch, $now, $onUnrated, &$rated, &$unrated, &$lastId): void {
                foreach ($batch as $event) {
                    $lastId = (int) $this->em->getUnitOfWork()->getSingleIdentifierValue($event);
                    $row = $this->pricing->effectiveRow($event->providerCode(), $event->modelCode(), $event->ts());
                    if ($row === null) {
                        ++$unrated;
                        $onUnrated($event);
                        continue;
                    }
                    $event->rateWith($row, $now);
                    ++$rated;
                }
            });
            $this->em->clear();
        }

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
