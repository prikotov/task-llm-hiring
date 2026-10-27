<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(columns: ['provider_code', 'provider_request_id'])]
class Usage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 50)]
    public string $providerCode;

    #[ORM\Column(length: 100)]
    public string $providerRequestId;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(length: 100)]
    public string $userName;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Model $model = null;

    #[ORM\Column(length: 50, nullable: true)]
    public ?string $modelCode = null;

    #[ORM\Column]
    public int $tokensInput = 0;

    #[ORM\Column]
    public int $tokensOutput = 0;

    #[ORM\Column]
    public int $tokensCacheRead = 0;

    #[ORM\Column]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(precision: 18, scale: 6, nullable: true)]
    public ?string $costUsd = null;
}
