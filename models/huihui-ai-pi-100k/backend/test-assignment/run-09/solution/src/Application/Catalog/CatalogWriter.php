<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\OffPeakPolicy;
use App\Domain\UnitRates;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентные upsert-операции справочника (используются и при полном
 * импорте цен, и при ретро-коррекции).
 */
final class CatalogWriter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function providerId(string $code): ?int
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);

        return $provider?->getId();
    }

    public function modelId(int $providerId, string $code): ?int
    {
        $model = $this->em->getRepository(Model::class)->findOneBy([
            'providerId' => $providerId,
            'code' => $code,
        ]);

        return $model?->getId();
    }

    public function upsertProvider(string $code, string $name, ?OffPeakPolicy $offPeak): Provider
    {
        $entity = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]) ?? new Provider();
        if ($entity->getId() === 0) {
            $this->em->persist($entity);
        }

        $entity->setCode($code);
        $entity->setName($name);
        if ($offPeak === null) {
            $entity->clearOffPeak();
        } else {
            // Окно нормализуем в минуты суток; множитель — decimal-строка (не float).
            $entity->setOffPeak($offPeak->startMinute(), $offPeak->endMinute(), $offPeak->multiplier());
        }

        return $entity;
    }

    public function upsertModel(int $providerId, string $code): Model
    {
        $entity = $this->em->getRepository(Model::class)->findOneBy([
            'providerId' => $providerId,
            'code' => $code,
        ]) ?? new Model();
        if ($entity->getId() === 0) {
            $this->em->persist($entity);
        }

        $entity->setProviderId($providerId);
        $entity->setCode($code);

        return $entity;
    }

    public function upsertPriceRow(
        int $providerId,
        int $modelId,
        DateTimeImmutable $validFrom,
        string $currency,
        UnitRates $rates,
    ): PriceRow {
        $entity = $this->em->getRepository(PriceRow::class)->findOneBy([
            'providerId' => $providerId,
            'modelId' => $modelId,
            'validFrom' => $validFrom,
        ]) ?? new PriceRow();
        if ($entity->getId() === 0) {
            $this->em->persist($entity);
        }

        $entity->setProviderId($providerId);
        $entity->setModelId($modelId);
        $entity->setValidFrom($validFrom);
        $entity->setCurrency($currency);
        $entity->applyRates($rates);

        return $entity;
    }
}
