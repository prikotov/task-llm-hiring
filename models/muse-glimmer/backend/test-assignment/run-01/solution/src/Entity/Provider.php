<?php

declare(strict_types=1);

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

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $offPeakStart = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $offPeakEnd = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getOffPeakStart(): ?\DateTime
    {
        return $this->offPeakStart;
    }

    public function setOffPeakStart(?\DateTime $offPeakStart): void
    {
        $this->offPeakStart = $offPeakStart;
    }

    public function getOffPeakEnd(): ?\DateTime
    {
        return $this->offPeakEnd;
    }

    public function setOffPeakEnd(?\DateTime $offPeakEnd): void
    {
        $this->offPeakEnd = $offPeakEnd;
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
