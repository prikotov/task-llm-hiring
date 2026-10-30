<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Pricing\EventRater;
use App\Domain\Pricing\PriceEntry;
use App\Domain\Pricing\UsageRecord;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Rates every not-yet-rated usage event (retries included), applying:
 *  - price row selection: max(valid_from) <= event ts;
 *  - billing scheme (per-token / unified);
 *  - provider off-peak multiplier (when the UTC time of day is inside the window);
 *  - the single final HALF_UP rounding to 6 digits.
 */
final class BillingRater
{
    private const FLUSH_EVERY = 200;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventRater $rater,
    ) {
    }

    /**
     * @return array{
     *     rated: int,
     *     unrated: list<array{provider: string, request_id: string, ts: \DateTimeImmutable}>
     * }
     */
    public function rate(): array
    {
        $events = $this->em->createQuery(
            'SELECT e FROM App\Entity\UsageEvent e
             WHERE e.status = :unrated
             ORDER BY e.provider, e.model, e.ts',
        )->setParameter('unrated', UsageEvent::STATUS_UNRATED)->getResult();

        $offPeaks = [];
        $priceRows = [];
        $rated = 0;
        $noPrice = [];

        $this->em->wrapInTransaction(function () use ($events, &$offPeaks, &$priceRows, &$rated, &$noPrice): void {
            foreach ($events as $event) {
                $key = $event->getProvider() . '|' . $event->getModel();

                $offPeaks[$event->getProvider()] ??= $this->offPeakOf($event->getProvider());
                $priceRows[$key] ??= $this->priceRows($event->getProvider(), $event->getModel());

                $priceRow = self::selectPriceRow($priceRows[$key], $event->getTs());

                if ($priceRow === null) {
                    $noPrice[] = [
                        'provider' => $event->getProvider(),
                        'request_id' => $event->getProviderRequestId(),
                        'ts' => $event->getTs(),
                    ];
                    continue;
                }

                $cost = $this->rater->rate($event->toUsageRecord(), $priceRow->toPriceEntry(), $offPeaks[$event->getProvider()]);
                $event->markRated($priceRow->getId(), $cost);

                if ((++$rated % self::FLUSH_EVERY) === 0) {
                    $this->em->flush();
                }
            }
            $this->em->flush();
        });

        return ['rated' => $rated, 'unrated' => $noPrice];
    }

    private function offPeakOf(string $providerCode): ?\App\Domain\Pricing\OffPeak
    {
        $provider = $this->em->find(Provider::class, $providerCode);

        return $provider?->offPeak();
    }

    /**
     * @return list<PriceRow>
     */
    private function priceRows(string $provider, string $model): array
    {
        return $this->em->createQuery(
            'SELECT p FROM App\Entity\PriceRow p
             WHERE p.provider = :provider AND p.model = :model
             ORDER BY p.validFrom ASC',
        )
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->getResult();
    }

    /**
     * Rule 1: the price row with the greatest valid_from <= ts (inclusive).
     *
     * @param list<PriceRow> $rows sorted by validFrom ascending
     */
    private static function selectPriceRow(array $rows, \DateTimeImmutable $ts): ?PriceRow
    {
        $best = null;
        foreach ($rows as $row) {
            if ($row->getValidFrom() <= $ts) {
                $best = $row;
            } else {
                break;
            }
        }

        return $best;
    }
}
