<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

use App\Billing\Domain\Usage\UsageEvent;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал корректировок стоимости события (после ретро-коррекции цены).
 * Сама стоимость события перезаписывается, но прежнее значение сохраняется здесь.
 */
#[ORM\Entity]
#[ORM\Table(name: 'billing_cost_adjustment')]
class CostAdjustment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    #[ORM\JoinColumn(name: 'usage_event_id', nullable: false)]
    private UsageEvent $event;

    #[ORM\Column(name: 'old_cost_usd', type: 'decimal', precision: 20, scale: 6)]
    private string $oldCostUsd;

    #[ORM\Column(name: 'new_cost_usd', type: 'decimal', precision: 20, scale: 6)]
    private string $newCostUsd;

    #[ORM\Column(length: 64)]
    private string $reason;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable', precision: 6)]
    private DateTimeImmutable $createdAt;

    public function __construct(UsageEvent $event, string $oldCostUsd, string $newCostUsd, string $reason, DateTimeImmutable $createdAt)
    {
        $this->event = $event;
        $this->oldCostUsd = $oldCostUsd;
        $this->newCostUsd = $newCostUsd;
        $this->reason = $reason;
        $this->createdAt = $createdAt;
    }
}
