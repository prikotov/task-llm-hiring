<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function getId(): ?int { return $this->id; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): void { $this->code = $code; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getOffPeakStart(): ?string { return $this->offPeakStart; }
    public function setOffPeakStart(?string $v): void { $this->offPeakStart = $v; }

    public function getOffPeakEnd(): ?string { return $this->offPeakEnd; }
    public function setOffPeakEnd(?string $v): void { $this->offPeakEnd = $v; }

    public function getOffPeakMultiplier(): ?string { return $this->offPeakMultiplier; }
    public function setOffPeakMultiplier(?string $v): void { $this->offPeakMultiplier = $v; }

    public function hasOffPeak(): bool
    {
        return $this->offPeakStart !== null && $this->offPeakEnd !== null && $this->offPeakMultiplier !== null;
    }
}
