<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): void { $this->code = $code; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getOffPeakStart(): ?string { return $this->offPeakStart; }
    public function setOffPeakStart(?string $t): void { $this->offPeakStart = $t; }
    public function getOffPeakEnd(): ?string { return $this->offPeakEnd; }
    public function setOffPeakEnd(?string $t): void { $this->offPeakEnd = $t; }
    public function getOffPeakMultiplier(): ?string { return $this->offPeakMultiplier; }
    public function setOffPeakMultiplier(?string $m): void { $this->offPeakMultiplier = $m; }
}
