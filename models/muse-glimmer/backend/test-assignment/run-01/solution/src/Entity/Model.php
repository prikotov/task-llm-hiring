<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_provider_model', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 50)]
    private string $providerCode;

    #[ORM\Column(name: 'code', type: 'string', length: 50)]
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
