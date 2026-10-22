<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_provider')]
class Provider
{
    #[ORM\Id, ORM\Column(length: 255)]
    public string $code;

    #[ORM\Column(type: 'text')]
    public string $name;

    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $offPeak = null;
}
