<?php

declare(strict_types=1);

namespace App\Repository\Billing;

use App\Entity\Billing\PriceRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceRow>
 */
final class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }
}
