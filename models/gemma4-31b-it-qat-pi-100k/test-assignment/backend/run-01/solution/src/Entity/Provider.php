<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'string', precision: 10, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function setOffPeakWindowStart(?string $offPeakWindowStart): void
    {
        $this->offPeakWindowStart = $offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function setOffPeakWindowEnd(?string $offPeakWindowEnd): void
    {
        $this->offPeakWindowEnd = $offPeakWindowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    public function setOffPeakMultiplier(?string $offPeakMultiplier): void
    {
        $this->offPeakMultiplier = $offPeakMultiplier;
    }
}
