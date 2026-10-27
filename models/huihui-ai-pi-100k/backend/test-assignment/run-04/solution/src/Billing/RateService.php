<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\CostCalculator;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки unrated).
 */
final class RateService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingStore $store,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * @return array{rated: int, unrated: int, unratedLines: string[]}
     */
    public function rateAll(): array
    {
        return $this->em->wrapInTransaction(function (): array {
            $events = $this->store->findUnratedEvents();

            $rated = 0;
            $unrated = 0;
            $unratedLines = [];
            $offPeakCache = [];

            foreach ($events as $event) {
                if (!($event instanceof UsageEvent)) {
                    continue;
                }

                $priceRow = $this->store->findLatestPrice($event->providerCode, $event->modelCode, $event->ts);
                if ($priceRow === null) {
                    $unrated++;
                    $unratedLines[] = sprintf(
                        'UNRATED %s/%s %s no_price',
                        $event->providerCode,
                        $event->providerRequestId,
                        $event->ts->format('Y-m-d\TH:i:s\Z')
                    );

                    continue;
                }

                $offPeak = $offPeakCache[$event->providerCode] ??= $this->offPeakOf($event->providerCode);

                $cost = $this->calculator->cost(
                    $event->tokensInput,
                    $event->tokensOutput,
                    $event->tokensCacheRead,
                    $event->tokensCacheWrite,
                    $priceRow->ratesAsArray(),
                    $offPeak,
                    $event->ts,
                );

                $this->store->markRated($event, $cost, $priceRow->id);
                $rated++;
            }

            $this->em->flush();

            return ['rated' => $rated, 'unrated' => $unrated, 'unratedLines' => $unratedLines];
        });
    }

    /**
     * @return array{start: string, end: string, multiplier: string}|null
     */
    private function offPeakOf(string $providerCode): ?array
    {
        $provider = $this->em->find(Provider::class, $providerCode);
        if (!$provider instanceof Provider || $provider->offPeakMultiplier === null) {
            return null;
        }

        return [
            'start' => (string) $provider->offPeakWindowStart,
            'end' => (string) $provider->offPeakWindowEnd,
            'multiplier' => (string) $provider->offPeakMultiplier,
        ];
    }
}
