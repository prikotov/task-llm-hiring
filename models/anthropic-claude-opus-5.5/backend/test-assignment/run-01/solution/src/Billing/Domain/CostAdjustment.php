<?php

declare(strict_types=1);

namespace App\Billing\Domain;

use App\Pricing\Domain\PriceRow;
use App\Shared\Decimal;
use App\Usage\Domain\UsageEvent;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал корректировок: фиксирует старую и новую стоимость события при ретро-коррекции цены.
 */
#[ORM\Entity]
#[ORM\Table(name: 'cost_adjustment')]
class CostAdjustment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::BIGINT)]
    private int|string|null $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 6)]
    private string $oldCostUsd;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 6)]
    private string $newCostUsd;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
        #[ORM\JoinColumn(nullable: false)]
        private UsageEvent $event,
        #[ORM\ManyToOne(targetEntity: PriceRow::class)]
        #[ORM\JoinColumn(nullable: false)]
        private PriceRow $priceRow,
        Decimal $oldCost,
        Decimal $newCost,
        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
        private DateTimeImmutable $createdAt,
    ) {
        $this->oldCostUsd = $oldCost->toFixed(6);
        $this->newCostUsd = $newCost->toFixed(6);
    }
}
