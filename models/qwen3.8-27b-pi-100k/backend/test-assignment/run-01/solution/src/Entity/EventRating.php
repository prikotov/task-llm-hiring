<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventRatingRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Результат тарификации события: по какой строке цен посчитано и какова
 * итоговая (округлённая до 6 знаков) стоимость.
 */
#[ORM\Entity(repositoryClass: EventRatingRepository::class)]
#[ORM\Table(name: 'event_ratings')]
#[ORM\UniqueConstraint(name: 'uq_event_ratings_event', columns: ['event_id'])]
final class EventRating
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

    #[ORM\Column(type: Types::STRING, length: 8)]
    private string $currency;

    /** Стоимость события, ровно 6 знаков после точки (десятичная строка). */
    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $cost;

    #[ORM\Column(name: 'rated_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $ratedAt;

    public function __construct(
        UsageEvent $event,
        PriceRow $priceRow,
        string $currency,
        string $cost,
        DateTimeImmutable $ratedAt,
    ) {
        $this->event = $event;
        $this->priceRow = $priceRow;
        $this->currency = $currency;
        $this->cost = $cost;
        $this->ratedAt = $ratedAt->setTimezone(new \DateTimeZone('UTC'));
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

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCost(): string
    {
        return $this->cost;
    }

    public function setCost(string $cost): void
    {
        $this->cost = $cost;
    }

    public function getRatedAt(): DateTimeImmutable
    {
        return $this->ratedAt;
    }
}
