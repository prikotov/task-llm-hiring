<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine\Entity;

use App\Billing\Domain\Money\Money;
use App\Billing\Domain\Usage\CostAdjustment;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал ретро-коррекций: что было, что стало и почему.
 * Сами события обновляются в usage_event — отчёт читает только его,
 * поэтому задвоения исключены по построению.
 */
#[ORM\Entity]
#[ORM\Table(name: 'cost_adjustment')]
#[ORM\Index(name: 'idx_cost_adjustment_event', columns: ['event_id'])]
final class CostAdjustmentRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(name: 'event_id', type: Types::BIGINT)]
    private int $eventId;

    #[ORM\Column(name: 'price_row_id', type: Types::INTEGER, nullable: true)]
    private ?int $priceRowId;

    #[ORM\Column(name: 'previous_cost_usd', type: Types::DECIMAL, precision: 30, scale: 6)]
    private string $previousCostUsd;

    #[ORM\Column(name: 'new_cost_usd', type: Types::DECIMAL, precision: 30, scale: 6)]
    private string $newCostUsd;

    #[ORM\Column(name: 'reason', type: Types::STRING, length: 255)]
    private string $reason;

    #[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public static function fromDomain(CostAdjustment $adjustment): self
    {
        $record = new self();
        $record->eventId = $adjustment->eventId;
        $record->priceRowId = $adjustment->newPriceRowId;
        $record->previousCostUsd = $adjustment->previousCost->roundHalfUp(Money::MONEY_SCALE)->toString();
        $record->newCostUsd = $adjustment->newCost->roundHalfUp(Money::MONEY_SCALE)->toString();
        $record->reason = $adjustment->reason;
        $record->createdAt = $adjustment->createdAt;

        return $record;
    }
}
