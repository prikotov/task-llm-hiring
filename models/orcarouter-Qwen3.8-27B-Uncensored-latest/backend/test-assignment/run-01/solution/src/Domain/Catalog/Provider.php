<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    public string $name = '';

    /** "HH:MM" UTC, включительно; null — оф-пиковой скидки нет. */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowStart = null;

    /** "HH:MM" UTC, исключительно; null — оф-пиковой скидки нет. */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowEnd = null;

    /** Десятичная строка (bcmath), например "0.5"; null — скидки нет. */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    public ?string $offPeakMultiplier = null;

    public function hasOffPeak(): bool
    {
        return $this->offPeakWindowStart !== null
            && $this->offPeakWindowEnd !== null
            && $this->offPeakMultiplier !== null;
    }
}
