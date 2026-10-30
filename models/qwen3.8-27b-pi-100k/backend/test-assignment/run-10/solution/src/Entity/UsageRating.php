<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Расчёт стоимости события, действующий с момента `effectiveFrom`
 * (эффективность во времени: ретро-коррекция цены не затрагивает старые
 * периоды, не переписывая уже показанные расходы).
 *
 * Стоимость — строка с ровно 6 знаками (DECIMAL(20,6)), float не используется.
 *
 * Инварианты:
 * — у rated-события последняя запись (по effective_from) не null;
 * — у unrated-события последняя запись — null (отсутствие цены на момент расчёта);
 * — записи строго не пересекаются по времени, первые — с 0.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_ratings')]
#[ORM\Index(name: 'idx_rating_event_seq', columns: ['event_id', 'effective_from'])]
#[ORM\UniqueConstraint(name: 'uniq_rating_event_effective', columns: ['event_id', 'effective_from'])]
class UsageRating extends AbstractEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: UsageEvent::class, inversedBy: 'ratings')]
        #[ORM\JoinColumn(name: 'event_id', nullable: false)]
        private UsageEvent $event,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $effectiveFrom,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $cost = null,
        #[ORM\ManyToOne(targetEntity: Price::class)]
        #[ORM\JoinColumn(name: 'price_id', nullable: true)]
        private ?Price $price = null,
        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $repriceAt = null,
    ) {
    }

    public function getEvent(): UsageEvent
    {
        return $this->event;
    }

    public function getEffectiveFrom(): \DateTimeImmutable
    {
        return $this->effectiveFrom;
    }

    /**
     * Стоимость в 6 знаках, null — unrated.
     */
    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function getCostDecimal(): \Decimal
    {
        if ($this->cost === null) {
            return \Decimal::fromInt(0);
        }

        return \Decimal::parse($this->cost);
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function getRepriceAt(): ?\DateTimeImmutable
    {
        return $this->repriceAt;
    }
}
