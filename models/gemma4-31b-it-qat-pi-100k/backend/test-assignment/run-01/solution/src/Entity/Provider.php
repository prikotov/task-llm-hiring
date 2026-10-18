<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    public string $code;

    #[ORM\Column(type: 'string', length: 255)]
    public string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    public ?string $offPeakMultiplier = null;

}
