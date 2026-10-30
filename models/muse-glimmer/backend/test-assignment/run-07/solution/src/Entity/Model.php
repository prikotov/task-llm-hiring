<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['provider_code', 'code'])
])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 64)]
    private string $code;

    public function __construct(string $providerCode, string $code)
    {
        $this->providerCode = $providerCode;
        $this->code = $code;
    }

    public function getId(): ?int { return $this->id; }
    public function getProviderCode(): string { return $this->providerCode; }
    public function getCode(): string { return $this->code; }
}
