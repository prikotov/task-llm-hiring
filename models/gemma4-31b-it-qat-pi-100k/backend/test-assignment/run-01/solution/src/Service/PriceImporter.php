<?php

namespace App\Service;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

class PriceImporter
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function import(string $filePath): void
    {
        $data = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);

        // Import Providers
        foreach ($data['providers'] as $pData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($pData['code']);
            if (!$provider) {
                $provider = new Provider();
                $provider->setCode($pData['code']);
            }
            $provider->setName($pData['name']);
            if ($pData['off_peak']) {
                $provider->setOffPeakWindowStart($pData['off_peak']['window_start']);
                $provider->setOffPeakWindowEnd($pData['off_peak']['window_end']);
                $provider->setOffPeakMultiplier($pData['off_peak']['multiplier']);
            }
            $this->entityManager->persist($provider);
        }
        $this->entityManager->flush();

        // Import Models
        foreach ($data['models'] as $mData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($mData['provider']);
            if (!$provider) {
                throw new \RuntimeException("Provider not found: {$mData['provider']}");
            }

            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $mData['code']
            ]);

            if (!$model) {
                $model = new Model();
                $model->setProvider($provider);
                $model->setCode($mData['code']);
                $this->entityManager->persist($model);
            }
        }
        $this->entityManager->flush();

        // Import Prices
        foreach ($data['prices'] as $prData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($prData['provider']);
            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $prData['model']
            ]);

            if (!$provider || !$model) {
                throw new \RuntimeException("Provider or Model not found for price: {$prData['provider']} / {$prData['model']}");
            }

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
    }
}
