<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал ретро-коррекции: фиксирует пересчёт стоимости конкретного события
 * (старая -> новая) и за счёт каких строк цен это сделано. Служит аудитом
 * для бонусной команды app:billing:reprice.
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_entries', indexes: [
    new ORM\Index(name: 'idx_reprice_entries_event', columns: ['usage_event_id']),
    new ORM\Index(name: 'idx_reprice_entries_price_row', columns: ['price_row_id']),
])]
final class RepriceEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(name: 'usage_event_id', type: 'bigint')]
    private int $usageEventId;

    #[ORM\Column(name: 'price_row_id', type: 'integer', nullable: true)]
    private ?int $priceRowId = null;

    #[ORM\Column(name: 'old_cost_usd', type: 'string')]
    private string $oldCostUsd;

    #[ORM\Column(name: 'new_cost_usd', type: 'string')]
    private string $newCostUsd;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(
        int $usageEventId,
        ?int $priceRowId,
        string $oldCostUsd,
        string $newCostUsd,
        DateTimeImmutable $createdAt,
    ) {
        $this->usageEventId = $usageEventId;
        $this->priceRowId = $priceRowId;
        $this->oldCostUsd = $oldCostUsd;
        $this->newCostUsd = $newCostUsd;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsageEventId(): int
    {
        return $this->usageEventId;
    }

    public function getPriceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function getOldCostUsd(): string
    {
        return $this->oldCostUsd;
    }

    public function getNewCostUsd(): string
    {
        return $this->newCostUsd;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
