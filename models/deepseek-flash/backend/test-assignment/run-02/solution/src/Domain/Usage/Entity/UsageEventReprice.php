<?php

declare(strict_types=1);

namespace App\Domain\Usage\Entity;

use App\Domain\Pricing\Entity\Price;
use App\Domain\Usage\ValueObject\RateStatus;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал ретро-коррекций: хранит, как изменилась стоимость события, чтобы
 * изменения уже показанных пользователю расходов не были «молчаливыми».
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_event_reprices')]
#[ORM\Index(name: 'idx_usage_event_reprices_event', columns: ['usage_event_id'])]
final class UsageEventReprice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    #[ORM\JoinColumn(name: 'usage_event_id', nullable: false, onDelete: 'CASCADE')]
    private UsageEvent $usageEvent;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'old_price_id', nullable: true, onDelete: 'SET NULL')]
    private ?Price $oldPrice;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'new_price_id', nullable: true, onDelete: 'SET NULL')]
    private ?Price $newPrice;

    #[ORM\Column(name: 'old_cost_usd', type: 'decimal', precision: 24, scale: 6, nullable: true)]
    private ?string $oldCostUsd;

    #[ORM\Column(name: 'new_cost_usd', type: 'decimal', precision: 24, scale: 6, nullable: true)]
    private ?string $newCostUsd;

    #[ORM\Column(name: 'old_status', type: 'string', length: 16, enumType: RateStatus::class)]
    private RateStatus $oldStatus;

    #[ORM\Column(name: 'new_status', type: 'string', length: 16, enumType: RateStatus::class)]
    private RateStatus $newStatus;

    #[ORM\Column(name: 'repriced_at', type: 'datetimetz_immutable')]
    private DateTimeImmutable $repricedAt;

    public function __construct(
        UsageEvent $usageEvent,
        ?Price $oldPrice,
        ?Price $newPrice,
        ?string $oldCostUsd,
        ?string $newCostUsd,
        RateStatus $oldStatus,
        RateStatus $newStatus,
        DateTimeImmutable $repricedAt,
    ) {
        $this->usageEvent = $usageEvent;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
        $this->oldCostUsd = $oldCostUsd;
        $this->newCostUsd = $newCostUsd;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->repricedAt = $repricedAt;
    }
}
