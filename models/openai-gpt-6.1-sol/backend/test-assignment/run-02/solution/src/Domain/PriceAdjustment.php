<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_price_adjustment')]
class PriceAdjustment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public private(set) ?int $id = null;

    #[ORM\Column(type: 'utc_instant')]
    public private(set) \DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)]
        public private(set) UsageEvent $event,
        #[ORM\Column(type: 'decimal', precision: 50, scale: 6)]
        public private(set) string $oldCost,
        #[ORM\Column(type: 'decimal', precision: 50, scale: 6)]
        public private(set) string $newCost,
    ) {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function delta(): string
    {
        return bcsub($this->newCost, $this->oldCost, 6);
    }
}
