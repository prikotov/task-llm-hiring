<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_model')]
#[ORM\UniqueConstraint(name: 'model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public private(set) ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)]
        public private(set) Provider $provider,
        #[ORM\Column(length: 255)]
        public private(set) string $code,
    ) {
    }
}
