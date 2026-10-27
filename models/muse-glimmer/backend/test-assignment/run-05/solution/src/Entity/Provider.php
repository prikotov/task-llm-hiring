<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(length: 50, unique: true)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code){ $this->code=$code; }

    public function getCode(): string { return $this->code; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name=$name; }
    public function getOffPeakWindowStart(): ?string { return $this->offPeakWindowStart; }
    public function setOffPeakWindowStart(?string $v): void { $this->offPeakWindowStart=$v; }
    public function getOffPeakWindowEnd(): ?string { return $this->offPeakWindowEnd; }
    public function setOffPeakWindowEnd(?string $v): void { $this->offPeakWindowEnd=$v; }
    public function getOffPeakMultiplier(): ?string { return $this->offPeakMultiplier; }
    public function setOffPeakMultiplier(?string $v): void { $this->offPeakMultiplier=$v; }
}
