<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Billing;
use App\Billing\Entity\Provider;
use App\Billing\Entity\UsageEvent;
use App\Billing\Pricing\RateCalculator;
use App\Billing\Repository\PriceRowRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Результаты одного запуска тарификации.
 *
 * @param int            $rated   рассчитано в этом запуске
 * @param list<UsageEvent> $unrated остались unrated в этом запуске (повторная попытка — на следующем rate)
 */
final class RateRunResult
{
    /** @param list<UsageEvent> $unrated */
    public function __construct(
        public readonly int $rated,
        public readonly array $unrated,
    ) {
    }
}
