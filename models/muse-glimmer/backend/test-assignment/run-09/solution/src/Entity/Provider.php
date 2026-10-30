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
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
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
}
