<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

final class ModelRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function findByProviderAndCode(Provider $provider, string $code): ?Model
    {
        return $this->em->getRepository(Model::class)->findOneBy([
            'provider' => $provider,
            'code' => $code,
        ]);
    }

    /**
     * Поиск по значениюм естественного ключа из внешнего потока
     * (provider.code + model.code).
     */
    public function findByProviderCodeAndCode(string $providerCode, string $modelCode): ?Model
    {
        $query = $this->em->createQueryBuilder()
            ->select('m')
            ->from(Model::class, 'm')
            ->innerJoin('m.provider', 'p')
            ->where('p.code = :providerCode')
            ->andWhere('m.code = :modelCode')
            ->setMaxResults(1)
            ->getQuery();
        $query->setParameter('providerCode', $providerCode);
        $query->setParameter('modelCode', $modelCode);

        return $query->getOneOrNullResult();
    }
}
