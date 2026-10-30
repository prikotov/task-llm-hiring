<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Price;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class BillingRatingService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function rateEvents(): array
    {
        $unrated = $this->entityManager->getRepository(UsageEvent::class)->findBy([
            'costUsd' => null,
        ]);

        $ratedCount = 0;
        $unratedCount = 0;
        $unratedEvents = [];

        foreach ($unrated as $event) {
            $price = $this->findApplicablePrice($event);

            if (!$price) {
                $unratedCount++;
                $unratedEvents[] = $event;
                continue;
            }

            $cost = $this->calculateCost($event, $price);
            $event->setCostUsd($cost);
            $ratedCount++;
        }

        $this->entityManager->flush();

        return [
            'rated' => $ratedCount,
            'unrated' => $unratedCount,
            'unrated_events' => $unratedEvents,
        ];
    }

    private function findApplicablePrice(UsageEvent $event): ?Price
    {
        return $this->entityManager->createQuery(
            'SELECT p FROM App\Entity\Price p 
             WHERE p.model = :model 
             AND p.validFrom <= :ts 
             ORDER BY p.validFrom DESC'
        )
        ->setParameter('model', $event->getModel())
        ->setParameter('ts', $event->getTs())
        ->setMaxResults(1)
        ->getOneOrNullResult();
    }

    private function calculateCost(UsageEvent $event, Price $price): string
    {
        $rates = $price->getRates();
        $cost = '0';

        if ($price->getUnit() === 'per_1m_tokens') {
            $cost = bcadd($cost, bcmul((string)$event->getTokensInput(), $rates['input'] ?? '0', 16));
            $cost = bcadd($cost, bcmul((string)$event->getTokensOutput(), $rates['output'] ?? '0', 16));
            $cost = bcadd($cost, bcmul((string)$event->getTokensCacheRead(), $rates['cache_read'] ?? '0', 16));
            $cost = bcadd($cost, bcmul((string)$event->getTokensCacheWrite(), $rates['cache_write'] ?? '0', 16));
        } elseif ($price->getUnit() === 'per_1m_tokens_unified') {
            $totalTokens = (string)($event->getTokensInput() + $event->getTokensOutput() + $event->getTokensCacheRead() + $event->getTokensCacheWrite());
            $cost = bcmul($totalTokens, $rates['any'] ?? '0', 16);
        }

        // Cost is currently per token, but rate is per 1M tokens.
        $cost = bcdiv($cost, '1000000', 16);

        // Apply off-peak
        $provider = $event->getProvider();
        if ($provider->hasOffPeak()) {
            if ($this->isOffPeak($event->getTs(), $provider->getOffPeakWindowStart(), $provider->getOffPeakWindowEnd())) {
                $cost = bcmul($cost, $provider->getOffPeakMultiplier() ?? '1', 16);
            }
        }

        return $this->bcRoundHalfUp($cost, 6);
    }

    private function isOffPeak(\DateTimeImmutable $ts, string $start, string $end): bool
    {
        $time = $ts->format('H:i');
        
        if ($start < $end) {
            return $time >= $start && $time < $end;
        } else {
            // Wraps around midnight
            return $time >= $start || $time < $end;
        }
    }

    private function bcRoundHalfUp(string $value, int $precision): string
    {
        if (bccomp($value, '0', 16) === 0) {
            return number_format(0, $precision, '.', '');
        }

        $adder = '0.' . str_repeat('0', $precision) . '5';
        
        if (bccomp($value, '0', 16) >= 0) {
            return bcadd($value, $adder, $precision);
        } else {
            return bcsub($value, $adder, $precision);
        }
    }
}
