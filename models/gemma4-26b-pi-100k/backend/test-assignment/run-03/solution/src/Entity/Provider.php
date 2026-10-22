<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
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
