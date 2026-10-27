<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider_code', 'code'])]
final class Model
{
    #[ORM\Id]
    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';

    public function key(): string
    {
        return $this->providerCode.'/'.$this->code;
    }
}
