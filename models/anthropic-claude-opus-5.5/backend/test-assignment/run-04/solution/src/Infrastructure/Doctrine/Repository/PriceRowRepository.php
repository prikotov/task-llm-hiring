<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Pricing\PriceRow;
use App\Infrastructure\Doctrine\Entity\ModelEntity;
use App\Infrastructure\Doctrine\Entity\PriceRowEntity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class PriceRowRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function findOne(ModelEntity $model, DateTimeImmutable $validFrom): ?PriceRowEntity
    {
        return $this->em->getRepository(PriceRowEntity::class)->findOneBy([
            'model' => $model,
            'validFrom' => $validFrom,
        ]);
    }

    /**
     * Upsert по (provider, model, valid_from): повторная загрузка тех же
     * данных не меняет состояние, загрузка изменённых — обновляет строку
     * на месте (это и есть механизм ретро-коррекции).
     */
    public function upsert(ModelEntity $model, PriceRow $row): PriceRowEntity
    {
        $entity = $this->findOne($model, $row->validFrom);
        if (null !== $entity) {
            $entity->applyFromDomain($row);

            return $entity;
        }

        $entity = new PriceRowEntity(
            $model,
            $row->validFrom,
            $row->currency,
            $row->unit->value,
            array_map(static fn ($rate): string => $rate->fixed(6), $row->rates()),
        );
        $this->em->persist($entity);

        return $entity;
    }

    /**
     * Все строки цен модели, от старых к новым.
     *
     * @return list<PriceRowEntity>
     */
    public function findAllForModel(ModelEntity $model): array
    {
        return $this->em->getRepository(PriceRowEntity::class)
            ->createQueryBuilder('p')
            ->andWhere('p.model = :model')
            ->setParameter('model', $model)
            ->orderBy('p.validFrom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
