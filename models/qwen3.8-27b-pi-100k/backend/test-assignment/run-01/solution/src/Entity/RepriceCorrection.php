<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Журнал корректировок (app:billing:reprice): по каждому пересчитанному
 * событию — старая и новая стоимость. Аудит; отчёт использует актуальные
 * стоимости из event_ratings (без задвоений).
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_corrections')]
final class RepriceCorrection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    private UsageEvent $event;

    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: false)]
    private PriceRow $priceRow;

    #[ORM\Column(name: 'old_cost', type: Types::STRING, length: 32)]
    private string $oldCost;

    #[ORM\Column(name: 'new_cost', type: Types::STRING, length: 32)]
    private string $newCost;

    #[ORM\Column(name: 'applied_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $appliedAt;

    public function __construct(
        UsageEvent $event,
        PriceRow $priceRow,
        string $oldCost,
        string $newCost,
        DateTimeImmutable $appliedAt,
    ) {
        $this->event = $event;
        $this->priceRow = $priceRow;
        $this->oldCost = $oldCost;
        $this->newCost = $newCost;
        $this->appliedAt = $appliedAt->setTimezone(new \DateTimeZone('UTC'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): UsageEvent
    {
        return $this->event;
    }

    public function getPriceRow(): PriceRow
    {
        return $this->priceRow;
    }

    public function getOldCost(): string
    {
        return $this->oldCost;
    }

    public function getNewCost(): string
    {
        return $this->newCost;
    }

    public function getAppliedAt(): DateTimeImmutable
    {
        return $this->appliedAt;
    }
}
