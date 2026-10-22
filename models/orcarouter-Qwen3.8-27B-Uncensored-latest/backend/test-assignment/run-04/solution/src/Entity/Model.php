<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    public function __construct(
        #[ORM\Column(type: 'string', length: 64)]
        private string $provider,

        #[ORM\Column(type: 'string', length: 64)]
        private string $code,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function key(): string
    {
        return $this->provider . '/' . $this->code;
    }
}
