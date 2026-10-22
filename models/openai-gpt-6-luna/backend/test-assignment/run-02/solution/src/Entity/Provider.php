<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(name: 'code', type: 'string', length: 120)]
    public string $code;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public string $name;

    #[ORM\Column(name: 'off_peak_start', type: 'string', length: 5, nullable: true)]
    public ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: 'string', length: 5, nullable: true)]
    public ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 36, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
