<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Decimal;
use App\Util\DateTimeUtc;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал корректировок цены (app:billing:reprice):
 * для каждого пересчитанного события хранится старая и новая стоимость.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_corrections')]
#[ORM\Index(name: 'idx_price_corrections_event', columns: ['usage_event_id'])]
final class PriceCorrection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    private UsageEvent $usageEvent;

    /** Строка цен (корректируемая), по которой событие пересчитано. */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    private PriceRow $priceRow;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 6)]
    private string $oldCost;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 6)]
    private string $newCost;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(UsageEvent $usageEvent, PriceRow $priceRow, Decimal $oldCost, Decimal $newCost)
    {
        $this->usageEvent = $usageEvent;
        $this->priceRow = $priceRow;
        $this->oldCost = $oldCost->format(6);
        $this->newCost = $newCost->format(6);
        $this->createdAt = DateTimeUtc::now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsageEvent(): UsageEvent
    {
        return $this->usageEvent;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
