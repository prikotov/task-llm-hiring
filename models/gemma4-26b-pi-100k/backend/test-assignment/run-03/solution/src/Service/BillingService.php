<?php

namespace App\Service;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class BillingService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function rateEvents(): array
    {
        $unratedEvents = $this->entityManager->getRepository(UsageEvent::class)->findBy(['isRated' => false]);

        $ratedCount = 0;
        $unratedCount = 0;
        $unratedDetails = [];

        foreach ($unratedEvents as $event) {
            $price = $this->findPriceForEvent($event);

            if ($price === null) {
                $unratedCount++;
                $unratedDetails[] = sprintf(
                    "UNRATED %s/%s %s no_price",
                    $event->getProvider(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format(\DateTimeInterface::ATOM)
                );
                continue;
            }

            $cost = $this->calculateCost($event, $price);
            $event->setCostUsd($cost);
            $event->setRated(true);
            $ratedCount++;
        }

        $this->entityManager->flush();

        return [
            'rated' => $ratedCount,
            'unrated' => $unratedCount,
            'unrated_details' => $unratedDetails
        ];
    }

    private function findPriceForEvent(UsageEvent $event): ?Price
    {
        $repo = $this->entityManager->getRepository(Price::class);
        
        $prices = $repo->findBy([
            'providerCode' => $event->getProvider(),
            'modelCode' => $event->getModelCode(),
        ]);

        if (empty($prices)) {
            return null;
        }

        usort($prices, fn(Price $a, Price $b) => $b->getValidFrom() <=> $a->getValidFrom());

        $eventTs = $event->getTs();
        foreach ($prices as $price) {
            if ($price->getValidFrom() <= $eventTs) {
                return $price;
            }
        }

        return null;
    }

    private function calculateCost(UsageEvent $event, Price $price): string
    {
        $rates = $price->getRates();
        $cost = '0';

        if ($price->getUnit() === 'per_1m_tokens') {
            $cost = '0';
            $cost = bcadd($cost, bcmul((string)$event->getTokensInput(), $rates['input'] ?? '0', 18), 18);
            $cost = bcadd($cost, bcmul((string)$event->getTokensOutput(), $rates['output'] ?? '0', 18), 18);
            $cost = bcadd($cost, bcmul((string)$event->getTokensCacheRead(), $rates['cache_read'] ?? '0', 18), 18);
            $cost = bcadd($cost, bcmul((string)$event->getTokensCacheWrite(), $rates['cache_write'] ?? '0', 18), 18);
            $cost = bcdiv($cost, '1000000', 18);
        } elseif ($price->getUnit() === 'per_1m_tokens_unified') {
            $totalTokens = bcadd(bcadd(bcadd((string)$event->getTokensInput(), (string)$event->getTokensOutput(), 0), (string)$event->getTokensCacheRead(), 0), (string)$event->getTokensCacheWrite(), 0);
            $cost = bcdiv(bcmul($totalTokens, $rates['any'] ?? '0', 18), '1000000', 18);
        }

        $provider = $this->entityManager->getRepository(Provider::class)->find($event->getProvider());
        if ($provider !== null && $provider->getOffPeakWindowStart() !== null) {
            if ($this->isOffPeak($event->getTs(), $provider)) {
                $multiplier = $provider->getOffPeakMultiplier() ?? '1';
                $cost = bcmul($cost, $multiplier, 18);
            }
        }

        return $this->bcRoundHalfUp($cost, 6);
    }

    private function isOffPeak(\DateTime $ts, Provider $provider): bool
    {
        $startStr = $provider->getOffPeakWindowStart();
        $endStr = $provider->getOffPeakWindowEnd();

        if (!$startStr || !$endStr) {
            return false;
        }

        $tsTime = $ts->format('H:i');

        if ($startStr < $endStr) {
            return $tsTime >= $startStr && $tsTime < $endStr;
        } else {
            return $tsTime >= $startStr || $tsTime < $endStr;
        }
    }

    private function bcRoundHalfUp(string $number, int $precision): string
    {
        if (strpos($number, '.') === false) {
            return bcadd($number, '0', $precision);
        }

        $add = '0.' . str_repeat('0', $precision) . '5';
        return bcadd($number, $add, $precision);
    }
}
