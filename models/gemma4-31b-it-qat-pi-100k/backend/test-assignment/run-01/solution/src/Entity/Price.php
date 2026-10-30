<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(columns: ['model_id', 'valid_from'])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    public Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    public string $currency;

    #[ORM\Column(type: 'string', length: 50)]
    public string $unit;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $rateInput = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $rateOutput = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $rateCacheRead = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $rateCacheWrite = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $rateAny = null;
}
