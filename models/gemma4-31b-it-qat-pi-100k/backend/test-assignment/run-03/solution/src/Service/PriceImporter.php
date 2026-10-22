<?php

namespace App\Service;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class PriceImporter
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function import(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        foreach ($data['providers'] ?? [] as $pData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($pData['code']);
            if (!$provider) {
                $provider = new Provider();
                $provider->code = $pData['code'];
            }
            $provider->name = $pData['name'];
            $offPeak = $pData['off_peak'] ?? null;
            $provider->offPeakWindowStart = $offPeak['window_start'] ?? null;
            $provider->offPeakWindowEnd = $offPeak['window_end'] ?? null;
            $provider->offPeakMultiplier = $offPeak['multiplier'] ?? null;
            $this->entityManager->persist($provider);
        }

        $this->entityManager->flush();

        foreach ($data['models'] ?? [] as $mData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($mData['provider']);
            if (!$provider) {
                throw new \RuntimeException("Provider {$mData['provider']} not found. Providers must be listed first.");
            }
            
            // Find model by provider and code
            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $mData['code']
            ]);
            
            if (!$model) {
                $model = new Model();
                $model->provider = $provider;
                $model->code = $mData['code'];
                $this->entityManager->persist($model);
            }
        }

        $this->entityManager->flush();

        foreach ($data['prices'] ?? [] as $prData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($prData['provider']);
            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $prData['model']
            ]);
            
            if (!$provider || !$model) {
                throw new \RuntimeException("Provider or Model not found for price: {$prData['provider']}/{$prData['model']}");
            }

            $validFrom = new DateTimeImmutable($prData['valid_from']);
            
            $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $validFrom
            ]);

            if (!$price) {
                $price = new Price();
                $price->provider = $provider;
                $price->model = $model;
                $price->validFrom = $validFrom;
                $this->entityManager->persist($price);
            }

            $price->currency = $prData['currency'];
            $price->unit = $prData['unit'];
            $rates = $prData['rates'];
            $price->rateInput = $rates['input'] ?? null;
            $price->rateOutput = $rates['output'] ?? null;
            $price->rateCacheRead = $rates['cache_read'] ?? null;
            $price->rateCacheWrite = $rates['cache_write'] ?? null;
            $price->rateAny = $rates['any'] ?? null;
        }

        $this->entityManager->flush();
    }
}
