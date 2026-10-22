<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Audit journal of retroactive price corrections. The report always reads the
 * current cost from usage_events; this table exists to make changes traceable.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_event_reprices')]
#[ORM\Index(name: 'idx_usage_event_reprices_event', columns: ['usage_event_id'])]
final class UsageEventReprice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    #[ORM\JoinColumn(name: 'usage_event_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UsageEvent $usageEvent;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'old_price_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Price $oldPrice;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'new_price_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private Price $newPrice;

    #[ORM\Column(name: 'old_cost_usd', type: 'decimal', precision: 24, scale: 6)]
    private string $oldCostUsd;

    #[ORM\Column(name: 'new_cost_usd', type: 'decimal', precision: 24, scale: 6)]
    private string $newCostUsd;

    #[ORM\Column(name: 'corrected_at', type: 'datetimetz_immutable', precision: 6)]
    private DateTimeImmutable $correctedAt;

    public function __construct(
        UsageEvent $usageEvent,
        ?Price $oldPrice,
        Price $newPrice,
        string $oldCostUsd,
        string $newCostUsd,
        DateTimeImmutable $correctedAt,
    ) {
        $this->usageEvent = $usageEvent;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
        $this->oldCostUsd = $oldCostUsd;
        $this->newCostUsd = $newCostUsd;
        $this->correctedAt = $correctedAt;
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
