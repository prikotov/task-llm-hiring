<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_usage')]
#[ORM\UniqueConstraint(columns: ['provider', 'request_id'])]
#[ORM\Index(columns: ['cost', 'occurred_at'])]
class Usage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public ?int $id = null;

    // События могут поступить раньше справочника: внешних ключей здесь нет.
    #[ORM\Column(length: 255)]
    public string $provider;

    #[ORM\Column(length: 255)]
    public string $requestId;

    #[ORM\Column(length: 255)]
    public string $model;

    #[ORM\Column(type: 'text')]
    public string $userId;

    #[ORM\Column(length: 27)]
    public string $occurredAt;

    #[ORM\Column(type: 'json')]
    public array $tokens;

    #[ORM\Column(type: 'decimal', precision: 100, scale: 6, nullable: true)]
    public ?string $cost = null;

    #[ORM\ManyToOne]
    public ?Price $price = null;
}
