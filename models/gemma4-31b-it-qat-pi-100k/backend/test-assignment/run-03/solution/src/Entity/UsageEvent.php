<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(columns: ['provider_code', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    public string $providerCode;

    #[ORM\Column(type: 'string', length: 255)]
    public string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', referencedColumnName: 'id', nullable: true)]
    public ?Price $price = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: true)]
    public ?Model $model = null;

    #[ORM\Column(type: 'string', length: 50)]
    public string $modelCode;


    #[ORM\Column(type: 'string', length: 255)]
    public string $userName;

    #[ORM\Column(type: 'datetime_immutable')]
    public DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    public ?string $costUsd = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?DateTimeImmutable $ratedAt = null;
}
