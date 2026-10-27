<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<PriceRow>
 */
final class PriceRowRepository extends EntityRepository
{
}
