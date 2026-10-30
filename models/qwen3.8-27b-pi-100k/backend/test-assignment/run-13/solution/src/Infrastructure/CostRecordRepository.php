<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<CostRecord>
 */
final class CostRecordRepository extends EntityRepository
{
    public function forEvent(int $eventId): ?CostRecord
    {
        return $this->findOneBy(['eventId' => $eventId]);
    }
}
