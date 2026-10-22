<?php

namespace App\Service;

use App\Entity\Price;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class BillingManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BillingService $billingService
    ) {}

    public function rateEvents(): array
    {
        $ratedCount = 0;
        $unratedEvents = [];

        $unratedEventsQuery = $this->entityManager->createQuery(
            'SELECT e FROM App\Entity\UsageEvent e WHERE e.costUsd IS NULL'
        );
        $events = $unratedEventsQuery->getResult();

        foreach ($events as $event) {
            $price = $this->findPriceForEvent($event);

            if ($price) {
                $cost = $this->billingService->calculateCost($event, $price);
                $event->costUsd = $cost;
                $event->model = $this->resolveModel($event);
                $event->price = $price;
                $event->ratedAt = new \DateTimeImmutable();
                $ratedCount++;
            } else {
                $unratedEvents[] = [
                    'id' => "{$event->providerCode}/{$event->providerRequestId}",
                    'ts' => $event->ts->format(\DateTimeInterface::ATOM),
                ];
            }
        }

        $this->entityManager->flush();

        return [
            'rated' => $ratedCount,
            'unrated' => $unratedEvents
        ];
    }

    public function reprice(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON');
        }

        $updatedPriceIds = [];
        
        foreach ($data['prices'] ?? [] as $prData) {
            $provider = $this->entityManager->getRepository(\App\Entity\Provider::class)->find($prData['provider']);
            if (!$provider) continue;

            $model = $this->entityManager->getRepository(\App\Entity\Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $prData['model']
            ]);
            if (!$model) continue;

            $validFrom = new \DateTimeImmutable($prData['valid_from']);
            $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $validFrom
            ]);

            if (!$price) continue;

            $price->unit = $prData['unit'];
            $rates = $prData['rates'];
            $price->rateInput = $rates['input'] ?? null;
            $price->rateOutput = $rates['output'] ?? null;
            $price->rateCacheRead = $rates['cache_read'] ?? null;
            $price->rateCacheWrite = $rates['cache_write'] ?? null;
            $price->rateAny = $rates['any'] ?? null;

            $updatedPriceIds[] = $price->id;
        }

        $this->entityManager->flush();

        $repricedCount = 0;
        $totalDelta = '0';

        foreach ($updatedPriceIds as $priceId) {
            $events = $this->entityManager->getRepository(UsageEvent::class)->findBy([
                'price' => $this->entityManager->find(Price::class, $priceId)
            ]);

            foreach ($events as $event) {
                $oldCost = $event->costUsd;
                $price = $event->price;
                $newCost = $this->billingService->calculateCost($event, $price);
                
                if ($oldCost !== $newCost) {
                    $event->costUsd = $newCost;
                    $delta = bcsub($newCost, $oldCost, 6);
                    $totalDelta = bcadd($totalDelta, $delta, 6);
                    $repricedCount++;
                }
            }
        }

        $this->entityManager->flush();

        return [
            'count' => $repricedCount,
            'delta' => $totalDelta
        ];
    }

    private function findPriceForEvent(UsageEvent $event): ?Price
    {
        $provider = $this->entityManager->getRepository(\App\Entity\Provider::class)->find($event->providerCode);
        if (!$provider) return null;

        $model = $this->resolveModel($event);
        if (!$model) return null;

        $query = $this->entityManager->createQuery(
            'SELECT p FROM App\Entity\Price p 
             WHERE p.provider = :provider 
             AND p.model = :model 
             AND p.validFrom <= :ts 
             ORDER BY p.validFrom DESC'
        )->setParameters([
            'provider' => $provider,
            'model' => $model,
            'ts' => $event->ts
        ])->setMaxResults(1);

        $result = $query->getResult();
        return $result[0] ?? null;
    }

    private function resolveModel(UsageEvent $event): ?\App\Entity\Model
    {
        if ($event->model) return $event->model;
        
        $provider = $this->entityManager->getRepository(\App\Entity\Provider::class)->find($event->providerCode);
        if (!$provider) return null;

        return $this->entityManager->getRepository(\App\Entity\Model::class)->findOneBy([
            'provider' => $provider,
            'code' => $event->modelCode
        ]);
    }
}
