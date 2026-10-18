<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

class PriceImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function import(string $filePath): void
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException('Cannot read file: ' . $filePath);
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        $providersData = $data['providers'] ?? [];
        $modelsData = $data['models'] ?? [];
        $pricesData = $data['prices'] ?? [];

        foreach ($providersData as $pData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($pData['code']);
            if (!$provider) {
                $provider = new Provider($pData['code'], $pData['name']);
                $this->entityManager->persist($provider);
            } else {
                // For simplicity and idempotency of "upsert", we can update the name if it changed
                // but the task says "upsert: providers - by code"
                // Let's just update name and offpeak settings.
                $this->updateProvider($provider, $pData);
            }
        }

        foreach ($modelsData as $mData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($mData['provider']);
            if (!$provider) {
                throw new \RuntimeException('Provider not found: ' . $mData['provider']);
            }

            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $mData['code'],
            ]);

            if (!$model) {
                $model = new Model($provider, $mData['code']);
                $this->entityManager->persist($model);
            }
        }

        $this->entityManager->flush();

        foreach ($pricesData as $prData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($prData['provider']);
            if (!$provider) {
                throw new \RuntimeException('Provider not found: ' . $prData['provider']);
            }

            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $prData['model'],
            ]);
            if (!$model) {
                throw new \RuntimeException('Model not found: ' . $prData['provider'] . '/' . $prData['model']);
            }

            $validFrom = new \DateTimeImmutable($prData['valid_from']);

            $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                'model' => $model,
                'validFrom' => $validFrom,
            ]);

            if (!$price) {
                $price = new Price(
                    $provider,
                    $model,
                    $validFrom,
                    $prData['currency'],
                    $prData['unit'],
                    $prData['rates']
                );
                $this->entityManager->persist($price);
            } else {
                $price->setUnit($prData['unit']);
                $price->setRates($prData['rates']);
            }
        }

        $this->entityManager->flush();
    }

    private function updateProvider(Provider $provider, array $data): void
    {
        $provider->setName($data['name']);
        if (isset($data['off_peak'])) {
            $op = $data['off_peak'];
            $provider->setOffPeakWindowStart($op['window_start'] ?? null);
            $provider->setOffPeakWindowEnd($op['window_end'] ?? null);
            $provider->setOffPeakMultiplier($op['multiplier'] ?? null);
        } else {
            $provider->setOffPeakWindowStart(null);
            $provider->setOffPeakWindowEnd(null);
            $provider->setOffPeakMultiplier(null);
        }
    }
}
