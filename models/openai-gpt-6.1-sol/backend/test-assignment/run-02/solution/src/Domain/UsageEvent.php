<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_usage')]
#[ORM\UniqueConstraint(name: 'usage_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'usage_period', columns: ['occurred_at'])]
class UsageEvent
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public private(set) ?int $id = null;

    #[ORM\ManyToOne, ORM\JoinColumn(nullable: true)]
    public private(set) ?Price $ratedPrice = null;

    #[ORM\Column(type: 'decimal', precision: 50, scale: 6, nullable: true)]
    public private(set) ?string $costUsd = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public private(set) ?string $appliedMultiplier = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        public private(set) string $providerCode,
        #[ORM\Column(length: 255)]
        public private(set) string $providerRequestId,
        #[ORM\Column(length: 255)]
        public private(set) string $modelCode,
        #[ORM\Column(length: 255)]
        public private(set) string $userId,
        #[ORM\Column(type: 'utc_instant')]
        public private(set) \DateTimeImmutable $occurredAt,
        #[ORM\Embedded(class: TokenUsage::class, columnPrefix: 'tokens_')]
        public private(set) TokenUsage $tokens,
    ) {
    }

    public function rate(Price $price, CostCalculator $calculator): void
    {
        if ($this->costUsd !== null) {
            throw new \LogicException('Событие уже рассчитано.');
        }
        $this->ratedPrice = $price;
        $this->appliedMultiplier = $price->model->provider->multiplierAt($this->occurredAt);
        $this->costUsd = $calculator->calculate($this->tokens, $price, $this->appliedMultiplier);
    }

    public function reprice(CostCalculator $calculator): PriceAdjustment
    {
        if ($this->ratedPrice === null || $this->costUsd === null || $this->appliedMultiplier === null) {
            throw new \LogicException('Нельзя корректировать нерассчитанное событие.');
        }
        $oldCost = $this->costUsd;
        $this->costUsd = $calculator->calculate($this->tokens, $this->ratedPrice, $this->appliedMultiplier);

        return new PriceAdjustment($this, $oldCost, $this->costUsd);
    }
}
