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

    public function rateUnratedEvents(): array
    {
        $ratedCount = 0;
        $unratedCount = 0;
        $unratedDetails = [];

        $events = $this->entityManager->getRepository(UsageEvent::class)->findBy(['isRated' => false]);

        foreach ($events as $event) {
            $price = $this->findPriceForEvent($event);
            if (!$price) {
                $unratedCount++;
                $unratedDetails[] = $event;
                continue;
            }

            $cost = $this->calculateCost($event, $price);
            $event->calculatedCost = $cost;
            $event->price = $price;
            $event->isRated = true;
            $ratedCount++;
        }

        $this->entityManager->flush();

        return [
            'rated' => $ratedCount,
            'unrated' => $unratedCount,
            'unrated_events' => $unratedDetails,
        ];
    }

    public function findPriceForEvent(UsageEvent $event): ?Price
    {
        $repo = $this->entityManager->getRepository(Price::class);
        $prices = $repo->findBy(
            ['model' => $event->model],
            ['validFrom' => 'DESC']
        );

        foreach ($prices as $price) {
            if ($price->validFrom <= $event->ts) {
                return $price;
            }
        }

        return null;
    }

    private function calculateCost(UsageEvent $event, Price $price): string
    {
        $cost = '0.0000000000000000';
        $tokens_input = (string)$event->tokensInput;
        $tokens_output = (string)$event->tokensOutput;
        $tokens_cache_read = (string)$event->tokensCacheRead;
        $tokens_cache_write = (string)$event->tokensCacheWrite;

        if ($price->unit === 'per_1m_tokens') {
            $cost = bcadd($cost, bcmul($tokens_input, $price->rateInput ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul($tokens_output, $price->rateOutput ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul($tokens_cache_read, $price->rateCacheRead ?? '0', 16), 16);
            $cost = bcadd($cost, bcmul($tokens_cache_write, $price->rateCacheWrite ?? '0', 16), 16);
        } elseif ($price->unit === 'per_1m_tokens_unified') {
            $totalTokens = bcadd(bcadd(bcadd($tokens_input, $tokens_output, 0), $tokens_cache_read, 0), $tokens_cache_write, 0);
            $cost = bcmul($totalTokens, $price->rateAny ?? '0', 16);
        }

        $cost = bcdiv($cost, '1000000', 16);

        // Off-peak discount
        $provider = $event->model->provider;
        if ($provider->offPeakWindowStart && $provider->offPeakWindowEnd) {
            if ($this->isOffPeak($event->ts, $provider->offPeakWindowStart, $provider->offPeakWindowEnd)) {
                $cost = bcmul($cost, $provider->offPeakMultiplier ?? '1', 16);
            }
        }

        return $this->roundHalfUp($cost, 6);
    }

    private function isOffPeak(\DateTimeImmutable $ts, string $start, string $end): bool
    {
        $currentTime = $ts->format('H:i');
        
        if ($start <= $end) {
            return $currentTime >= $start && $currentTime < $end;
        } else {
            // Window crosses midnight
            return $currentTime >= $start || $currentTime < $end;
        }
    }

    public function reprice(array $correctionData): array
    {
        $repricedEventsCount = 0;
        $totalDelta = '0.000000';

        $importedPrices = [];
        
        // 1. Upsert prices and track which ones changed
        if (isset($correctionData['prices'])) {
            foreach ($correctionData['prices'] as $pData) {
                $provider = $this->entityManager->getRepository(Provider::class)->find($pData['provider']);
                if (!$provider) continue;
                
                $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                    'provider' => $provider,
                    'code' => $pData['model'],
                ]);
                if (!$model) continue;

                $validFrom = new \DateTimeImmutable($pData['valid_from']);
                $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                    'model' => $model,
                    'validFrom' => $validFrom,
                ]);

                if (!$price) {
                    $price = new Price();
                    $price->model = $model;
                    $price->validFrom = $validFrom;
                    $this->entityManager->persist($price);
                }

                $oldRates = [
                    'input' => $price->rateInput,
                    'output' => $price->rateOutput,
                    'cache_read' => $price->rateCacheRead,
                    'cache_write' => $price->rateCacheWrite,
                    'any' => $price->rateAny,
                ];

                $price->currency = $pData['currency'] ?? $price->currency;
                $price->unit = $pData['unit'] ?? $price->unit;
                $rates = $pData['rates'] ?? [];
                $price->rateInput = $rates['input'] ?? $price->rateInput;
                $price->rateOutput = $rates['output'] ?? $price->rateOutput;
                $price->rateCacheRead = $rates['cache_read'] ?? $price->rateCacheRead;
                $price->rateCacheWrite = $rates['cache_write'] ?? $price->rateCacheWrite;
                $price->rateAny = $rates['any'] ?? $price->rateAny;

                if ($oldRates !== [
                    'input' => $price->rateInput,
                    'output' => $price->rateOutput,
                    'cache_read' => $price->rateCacheRead,
                    'cache_write' => $price->rateCacheWrite,
                    'any' => $price->rateAny,
                ]) {
                    $importedPrices[] = $price;
                }
            }
            $this->entityManager->flush();
        }

        // 2. Recalculate events rated by these prices
        foreach ($importedPrices as $price) {
            $events = $this->entityManager->getRepository(UsageEvent::class)->findBy(['price' => $price]);
            foreach ($events as $event) {
                $oldCost = $event->calculatedCost;
                $newCost = $this->calculateCost($event, $price);
                
                if ($oldCost !== $newCost) {
                    $event->calculatedCost = $newCost;
                    $totalDelta = bcadd($totalDelta, bcsub($newCost, $oldCost, 6), 6);
                    $repricedEventsCount++;
                }
            }
        }
        
        $this->entityManager->flush();

        return [
            'count' => $repricedEventsCount,
            'delta' => $totalDelta,
        ];
    }

    private function roundHalfUp(string $value, int $precision): string

    {
        $multiplier = bcpow('10', (string)($precision + 1));
        $shifted = bcmul($value, $multiplier, 0);
        
        // Get the last digit for rounding
        $lastDigit = substr($shifted, -1);
        
        // we want to remove the last digit and add 1 if it's >= 5
        $truncated = substr($shifted, 0, -1);
        if ($lastDigit >= 5) {
            $truncated = bcadd($truncated, '1', 0);
        }
        
        $result = bcdiv($truncated, bcpow('10', (string)$precision), $precision);
        return $result;
    }
}
