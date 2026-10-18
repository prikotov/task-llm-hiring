<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    public string $providerCode;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    public string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    public Model $model;

    #[ORM\Column(type: 'string', length: 255)]
    public string $username;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheWrite = 0;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', referencedColumnName: 'id', nullable: true)]
    public ?Price $price = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $calculatedCost = null;

    #[ORM\Column(type: 'boolean')]
    public bool $isRated = false;
}
