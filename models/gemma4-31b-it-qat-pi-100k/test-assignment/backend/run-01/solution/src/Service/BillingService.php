<?php

namespace App\Service;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class BillingService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function rateAll(): array
    {
        $ratedCount = 0;
        $unratedCount = 0;
        $unratedEvents = [];

        $events = $this->entityManager->getRepository(UsageEvent::class)->findBy(
            ['costUsd' => null],
            ['ts' => 'ASC'],
            1000 // Process in batches if necessary, but for now let's try all
        );

        // We need all unrated events. findBy with null might not work perfectly with large sets.
        // Using a query.
        $events = $this->entityManager->createQuery('SELECT e FROM App\Entity\UsageEvent e WHERE e.costUsd IS NULL')
            ->getResult();

        foreach ($events as $event) {
            $price = $this->findApplicablePrice($event);

            if (!$price) {
                $unratedCount++;
                $unratedEvents[] = [
                    'id' => $event->getProvider() . '/' . $event->getProviderRequestId(),
                    'ts' => $event->getTs()->format(\DateTimeInterface::RFC3339),
                ];
                continue;
            }

            $cost = $this->calculateCost($event, $price);

            if ($this->isOffPeak($event, $price->getProvider())) {
                $multiplier = $price->getProvider()->getOffPeakMultiplier();
                $cost = bcmul($cost, $multiplier, 16);
            }

            $roundedCost = $this->roundHalfUp($cost, 6);
            $event->setCostUsd($roundedCost);
            $event->setPrice($price);
            $ratedCount++;
        }

        $this->entityManager->flush();

        return [
            'rated' => $ratedCount,
            'unrated' => $unratedCount,
            'unratedEvents' => $unratedEvents,
        ];
    }

    private function findApplicablePrice(UsageEvent $event): ?Price
    {
        $qb = $this->entityManager->createQueryBuilder();
        $price = $qb->select('p')
            ->from(Price::class, 'p')
            ->where('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('model', $this->findModelForEvent($event))
            ->setParameter('ts', $event->getTs())
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $price;
    }

    private function findModelForEvent(UsageEvent $event)
    {
        // The event stores model as string "provider/code" or just code? 
        // Let's check the usage import. It stores it as $data['model'].
        // In prices.json, it's just the code. But the event needs to match.
        // Wait, if usage.jsonl has "model": "lumen-8", and prices.json has "model": "lumen-8".
        
        $model = $this->entityManager->getRepository(Model::class)->findOneBy([
            'provider' => $this->entityManager->getRepository(Provider::class)->find($event->getProvider()),
            'code' => $event->getModel()
        ]);

        return $model;
    }

    private function calculateCost(UsageEvent $event, Price $price): string
    {
        $rates = $price->getRates();
        $cost = '0';

        if ($price->getUnit() === 'per_1m_tokens') {
            $cost = bcadd($cost, bcmul($event->getTokensInput(), $rates['input'] ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul($event->getTokensOutput(), $rates['output'] ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul($event->getTokensCacheRead(), $rates['cache_read'] ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul($event->getTokensCacheWrite(), $rates['cache_write'] ?? '0', 16), 16);
        } elseif ($price->getUnit() === 'per_1m_tokens_unified') {
            $totalTokens = (string)($event->getTokensInput() + $event->getTokensOutput() + $event->getTokensCacheRead() + $event->getTokensCacheWrite());
            $cost = bcmul($totalTokens, $rates['any'] ?? '0', 16);
        }

        return bcdiv($cost, '1000000', 16);
    }

    private function isOffPeak(UsageEvent $event, Provider $provider): bool
    {
        $windowStart = $provider->getOffPeakWindowStart();
        $windowEnd = $provider->getOffPeakWindowEnd();

        if (!$windowStart || !$windowEnd) {
            return false;
        }

        $ts = $event->getTs();
        $currentTime = $ts->format('H:i');

        if ($windowStart <= $windowEnd) {
            return $currentTime >= $windowStart && $currentTime < $windowEnd;
        } else {
            // Over midnight
            return $currentTime >= $windowStart || $currentTime < $windowEnd;
        }
    }

    private function roundHalfUp(string $number, int $precision): string
    {
        $multiplier = bcpow('10', (string)$precision);
        $shifted = bcmul($number, $multiplier, 20);
        $added = bcadd($shifted, '0.5', 20);
        $truncated = bcadd($added, '0', 0);
        return bcdiv($truncated, $multiplier, $precision);
    }

    public function reprice(string $filePath): array
    {
        $data = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);
        
        $repricedCount = 0;
        $delta = '0';

        foreach ($data['prices'] ?? [] as $prData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($prData['provider']);
            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $prData['model']
            ]);

            if (!$provider || !$model) continue;

            $validFrom = new \DateTimeImmutable($prData['valid_from']);
            $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $validFrom
            ]);

            if (!$price) {
                $price = new Price();
                $price->setProvider($provider);
                $price->setModel($model);
                $price->setValidFrom($validFrom);
                $this->entityManager->persist($price);
            }

            $price->setCurrency($prData['currency']);
            $price->setUnit($prData['unit']);
            $price->setRates($prData['rates']);
        }
        $this->entityManager->flush();

        $allRatedEvents = $this->entityManager->createQuery('SELECT e FROM App\Entity\UsageEvent e WHERE e.price IS NOT NULL')
            ->getResult();

        foreach ($allRatedEvents as $event) {
            $oldCost = $event->getCostUsd();
            $price = $event->getPrice();
            
            $cost = $this->calculateCost($event, $price);
            if ($this->isOffPeak($event, $price->getProvider())) {
                $multiplier = $price->getProvider()->getOffPeakMultiplier();
                $cost = bcmul($cost, $multiplier, 16);
            }
            $roundedCost = $this->roundHalfUp($cost, 6);

            if ($roundedCost !== $oldCost) {
                $event->setCostUsd($roundedCost);
                $repricedCount++;
                $delta = bcadd($delta, bcsub($roundedCost, $oldCost, 6), 6);
            }
        }
        $this->entityManager->flush();

        return [
            'count' => $repricedCount,
            'delta' => $delta,
        ];
    }
}
