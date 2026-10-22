<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 64)]
    private string $code;

    public function getId(): ?int { return $this->id; }

    public function getProviderCode(): string { return $this->providerCode; }
    public function setProviderCode(string $v): void { $this->providerCode = $v; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $v): void { $this->code = $v; }
}
