<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\RateCalculator;
use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий (с повторными попытками для
 * ранее unrated, т.к. цены могли подтянуться позже).
 */
final class RateService
{
    public function __construct(
        private readonly BillingRepository $repo,
        private readonly EntityManagerInterface $em,
        private readonly RateCalculator $calculator,
    ) {
    }

    /**
     * @return array{0: int, 1: UsageEvent[]} [рассчитано, оставшиеся unrated]
     */
    public function rate(): array
    {
        $unrated = $this->repo->findUnratedEvents();
        $ratedCount = 0;
        $stillUnrated = [];
        $rowsCache = [];

        foreach ($unrated as $event) {
            $provider = $event->getProvider();
            $model = $event->getModel();

            $cacheKey = $provider->getId() . '|' . $model->getCode();
            if (!isset($rowsCache[$cacheKey])) {
                $rowsCache[$cacheKey] = $this->repo->findPriceRowsForModel($provider, $model->getCode());
            }

            $bestRow = $this->selectApplicableRow($rowsCache[$cacheKey], $event);

            if (null === $bestRow) {
                $stillUnrated[] = $event;
                continue;
            }

            $offPeak = OffPeakWindow::fromProvider($provider);
            $cost = $this->calculator->calculate($event, $bestRow, $offPeak);
            $event->markRated($bestRow, $cost->amount());
            $ratedCount++;
        }

        $this->em->flush();

        return [$ratedCount, $stillUnrated];
    }

    /**
     * Строка цен модели с наибольшим valid_from <= ts события (valid_from включительно).
     *
     * @param PriceRow[] $rows
     */
    private function selectApplicableRow(array $rows, UsageEvent $event): ?PriceRow
    {
        $eventTs = $event->getTs();
        $best = null;

        foreach ($rows as $row) {
            if ($row->getValidFrom() <= $eventTs) {
                if (null === $best || $row->getValidFrom() > $best->getValidFrom()) {
                    $best = $row;
                }
            }
        }

        return $best;
    }
}
