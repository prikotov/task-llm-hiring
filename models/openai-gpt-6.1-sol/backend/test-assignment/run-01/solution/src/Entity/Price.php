<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_price')]
#[ORM\UniqueConstraint(columns: ['model_id', 'valid_from'])]
class Price
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)]
    public Model $model;

    #[ORM\Column(length: 27)]
    public string $validFrom;

    #[ORM\Column(length: 3)]
    public string $currency = 'USD';

    #[ORM\Column(length: 32)]
    public string $unit;

    #[ORM\Column(type: 'json')]
    public array $rates;
}
