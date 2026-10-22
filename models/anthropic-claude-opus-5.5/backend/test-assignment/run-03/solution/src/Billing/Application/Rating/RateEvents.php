<?php

declare(strict_types=1);

namespace App\Billing\Application\Rating;

use App\Billing\Domain\Catalog\Catalog;
use App\Billing\Domain\Pricing\PriceCatalog;
use App\Billing\Domain\Pricing\TariffBook;
use App\Billing\Domain\Usage\Rating;
use App\Billing\Domain\Usage\UsageEvent;
use App\Billing\Domain\Usage\UsageJournal;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий.
 *
 * Каждый запуск повторяет попытку для всех unrated — цены могли подтянуться
 * позже (TASK п. 7). Уже рассчитанные события не пересматриваются: тариф
 * зафиксирован в момент расчёта, изменение справочника — отдельная операция
 * (app:billing:reprice).
 */
final class RateEvents
{
    public function __construct(
        private readonly UsageJournal $journal,
        private readonly PriceCatalog $prices,
        private readonly Catalog $catalog,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): RateResult
    {
        $tariffBook = new TariffBook($this->prices->all(), $this->catalog->offPeakWindows());
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $rated = [];
        $unrated = [];

        foreach ($this->journal->unrated() as $event) {
            $quote = $tariffBook->quote($event->model, $event->usage, $event->occurredAt);

            if (null === $quote) {
                $unrated[] = $event;

                continue;
            }

            $rated[] = $event->withRating(new Rating(
                cost: $quote->cost,
                priceRowId: $quote->row->id,
                ratedAt: $now,
                appliedOffPeak: $quote->appliedOffPeak,
            ));
        }

        if ([] !== $rated) {
            $this->em->wrapInTransaction(fn () => $this->journal->saveRating($rated));
        }

        return new RateResult(count($rated), $unrated);
    }
}

/**
 * Итоги запуска: сколько рассчитано и какие события остались без цены.
 */
final readonly class RateResult
{
    /**
     * @param list<UsageEvent> $unrated
     */
    public function __construct(
        public int $rated,
        public array $unrated,
    ) {
    }
}
