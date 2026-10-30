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

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $offPeak = null;

    public function __construct(string $code, string $name, ?array $offPeak = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->offPeak = $offPeak;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOffPeak(): ?array
    {
        return $this->offPeak;
    }

    public function setOffPeak(?array $offPeak): void
    {
        $this->offPeak = $offPeak;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
