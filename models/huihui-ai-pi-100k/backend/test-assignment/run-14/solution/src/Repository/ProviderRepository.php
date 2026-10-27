<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\OffPeakWindow;
use App\Domain\WindowMinutes;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Справочник провайдеров (ключ — code).
 */
final class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function findByCode(string $code): ?Provider
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function upsert(string $code, string $name, ?OffPeakWindow $offPeak): Provider
    {
        $existing = $this->findByCode($code);

        if ($existing !== null) {
            $existing->name = $name;
            $this->applyOffPeak($existing, $offPeak);

            return $existing;
        }

        $provider = new Provider();
        $provider->code = $code;
        $provider->name = $name;
        $this->applyOffPeak($provider, $offPeak);
        $this->getEntityManager()->persist($provider);

        return $provider;
    }

    private function applyOffPeak(Provider $provider, ?OffPeakWindow $offPeak): void
    {
        if ($offPeak === null) {
            $provider->hasOffPeak = false;
            $provider->offPeakStartMinutes = 0;
            $provider->offPeakEndMinutes = 0;
            $provider->offPeakMultiplier = '1.000000';
            return;
        }

        $provider->hasOffPeak = true;
        $provider->offPeakStartMinutes = WindowMinutes::from($offPeak->windowStartLabel());
        $provider->offPeakEndMinutes = WindowMinutes::from($offPeak->windowEndLabel());
        $provider->offPeakMultiplier = $offPeak->getMultiplier();
    }
}
