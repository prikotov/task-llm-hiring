<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_model')]
#[ORM\UniqueConstraint(columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne, ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'code', nullable: false)]
    public Provider $provider;

    #[ORM\Column(length: 255)]
    public string $code;
}
