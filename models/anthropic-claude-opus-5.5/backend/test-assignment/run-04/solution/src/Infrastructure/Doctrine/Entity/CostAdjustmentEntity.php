<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал ретро-коррекций: что было, что стало и почему.
 * Нужен для аудита пересчётов, в отчёт не попадает
 * (отчёт всегда читает актуальную cost_usd события).
 *
 * event_id — ссылка на usage_events.id без FK: события не удаляются
 * (правила задачи это запрещают), а внешний ключ здесь ничего не защищает
 * и только усложняет миграции.
 */
#[ORM\Entity]
#[ORM\Table(name: 'cost_adjustments')]
#[ORM\Index(name: 'idx_cost_adjustments_event', columns: ['event_id'])]
final class CostAdjustmentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'event_id', type: 'integer')]
    private int $eventId;

    #[ORM\Column(name: 'old_cost_usd', type: 'decimal', precision: 24, scale: 6)]
    private string $oldCostUsd;

    #[ORM\Column(name: 'new_cost_usd', type: 'decimal', precision: 24, scale: 6)]
    private string $newCostUsd;

    #[ORM\Column(name: 'reason', type: 'string', length: 255)]
    private string $reason;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(int $eventId, string $oldCostUsd, string $newCostUsd, string $reason)
    {
        $this->eventId = $eventId;
        $this->oldCostUsd = $oldCostUsd;
        $this->newCostUsd = $newCostUsd;
        $this->reason = $reason;
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function eventId(): int
    {
        return $this->eventId;
    }

    public function oldCostUsd(): string
    {
        return $this->oldCostUsd;
    }

    public function newCostUsd(): string
    {
        return $this->newCostUsd;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
