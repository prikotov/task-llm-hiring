<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал корректировок `billing:reprice`: по одному факту пересчёта
 * на (событие, строка цен, момент корректировки). История не удаляется —
 * повторный reprice той же строки цен добавляет новую запись.
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_entries')]
#[ORM\Index(name: 'idx_reprice_entries_event', columns: ['usage_event'])]
#[ORM\Index(name: 'idx_reprice_entries_price_row', columns: ['price_row'])]
class RepriceEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(name: 'usage_event_id', type: 'bigint')]
    public int $usageEvent = 0;

    #[ORM\Column(name: 'price_row_id', type: 'integer')]
    public int $priceRow = 0;

    #[ORM\Column(name: 'old_cost_usd', type: 'decimal', precision: 34, scale: 6)]
    public string $oldCostUsd = '0.000000';

    #[ORM\Column(name: 'new_cost_usd', type: 'decimal', precision: 34, scale: 6)]
    public string $newCostUsd = '0.000000';

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;
}
