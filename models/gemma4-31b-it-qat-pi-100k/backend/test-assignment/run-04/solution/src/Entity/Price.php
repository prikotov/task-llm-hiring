<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(nullable: false)]
    public Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(length: 10)]
    public string $currency;

    #[ORM\Column(length: 50)]
    public string $unit;

    #[ORM\Column(type: 'json')]
    public array $rates;
}
