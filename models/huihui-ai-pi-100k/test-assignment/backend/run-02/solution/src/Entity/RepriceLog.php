<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Money;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Журнал ретро-коррекции цен: каждое пересчитанное событие
 * оставляет запись со старой и новой стоимостью.
 *
 * Само событие перезаписывается на месте (стоимость — новая),
 * поэтому дублирования в отчёте нет; журнал — для аудита.
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_log')]
final class RepriceLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class)]
    #[ORM\JoinColumn(name: 'event_id', nullable: false, onDelete: 'CASCADE')]
    public UsageEvent $event;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', nullable: true)]
    public ?Price $price = null;

    #[ORM\Column(name: 'old_cost', type: Types::DECIMAL, precision: 30, scale: 6)]
    public string $oldCost;

    #[ORM\Column(name: 'new_cost', type: Types::DECIMAL, precision: 30, scale: 6)]
    public string $newCost;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;

    public function __construct(UsageEvent $event, ?Price $price, Money $oldCost, Money $newCost)
    {
        $this->event = $event;
        $this->price = $price;
        $this->oldCost = $oldCost->toDecimalString();
        $this->newCost = $newCost->toDecimalString();
        $this->createdAt = new \DateTimeImmutable('UTC');
    }
}
