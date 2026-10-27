<?php

declare(strict_types=1);

namespace App\Repository\Billing;

use App\Entity\Billing\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Model>
 */
final class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findByName(string $providerCode, string $code): ?Model
    {
        /** @var EntityManagerInterface $em */
        $em = $this->_em;

        return $em->createQuery(
            'SELECT m FROM App\Entity\Billing\Model m
             JOIN m.provider p
             WHERE p.code = :provider AND m.code = :code',
        )
            ->setParameter('provider', $providerCode)
            ->setParameter('code', $code)
            ->getOneOrNullResult();
    }
}
