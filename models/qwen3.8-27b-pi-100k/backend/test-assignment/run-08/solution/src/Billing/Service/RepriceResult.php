<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Billing;
use App\Billing\Entity\Model;
use App\Billing\Entity\PriceRow;
use App\Billing\Entity\Provider;
use App\Billing\Import\Dto\PricesFile;
use App\Billing\Pricing\RateCalculator;
use App\Billing\Repository\PriceRowRepository;
use Doctrine\ORM\EntityManagerInterface;

/** Результат ретро-коррекции (бонус, app:billing:reprice). */
final class RepriceResult
{
    public function __construct(
        public readonly int $repricedEvents,
        /** Новая минус старая сумма стоимостей пересчитанных событий (6 знаков). */
        public readonly string $deltaUsd,
    ) {
    }
}
