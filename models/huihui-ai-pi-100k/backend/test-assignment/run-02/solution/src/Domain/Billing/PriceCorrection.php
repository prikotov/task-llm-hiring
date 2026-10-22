<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Pricing\PriceRow;
use App\Domain\Usage\UsageEvent;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Запись журнала ретро-корректировок: для каждого пересчитанного события
 * хранятся старая и новая стоимость. Итоговый отчёт читается из
 * usage_events (там уже новая стоимость), журнал — для аудита.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_corrections')]
final class PriceCorrection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\TypedIdentifier]
    #[ORM\Column(type: 'bigint')]
    private null|int $id = null;

    #[ORM\Column(name: 'old_cost', type: 'decimal', precision: 20, scale: 6)]
    private string $oldCost;

    #[ORM\Column(name: 'new_cost', type: 'decimal', precision: 20, scale: 6)]
    private string $newCost;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    private null|UsageEvent $usageEvent = null;

    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    private null|PriceRow $priceRow = null;

    public function __construct(
        null|UsageEvent $usageEvent,
        null|PriceRow $priceRow,
        string $oldCost,
        string $newCost,
    ) {
        $this->usageEvent = $usageEvent;
        $this->priceRow = $priceRow;
        $this->oldCost = $oldCost;
        $this->newCost = $newCost;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): null|int
    {
        return $this->id;
    }

    public function getOldCost(): string
    {
        return $this->oldCost;
    }

    public function getNewCost(): string
    {
        return $this->newCost;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUsageEvent(): null|UsageEvent
    {
        return $this->usageEvent;
    }

    public function getPriceRow(): null|PriceRow
    {
        return $this->priceRow;
    }
}
