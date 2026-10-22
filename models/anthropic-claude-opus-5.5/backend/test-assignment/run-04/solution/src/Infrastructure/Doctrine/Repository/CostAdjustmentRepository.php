<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Pricing\CostAdjustment;
use App\Infrastructure\Doctrine\Entity\CostAdjustmentEntity;
use Doctrine\ORM\EntityManagerInterface;

final class CostAdjustmentRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function add(CostAdjustment $adjustment): CostAdjustmentEntity
    {
        $entity = new CostAdjustmentEntity(
            $adjustment->eventId,
            $adjustment->oldCost->fixed(6),
            $adjustment->newCost->fixed(6),
            $adjustment->reason,
        );
        $this->em->persist($entity);

        return $entity;
    }
}
