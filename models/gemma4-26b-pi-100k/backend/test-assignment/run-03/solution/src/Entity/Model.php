<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'model_unique_idx', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $providerCode;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $code;

    public function __construct(string $providerCode, string $code)
    {
        $this->providerCode = $providerCode;
        $this->code = $code;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
