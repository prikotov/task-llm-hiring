<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 128)]
    public string $providerCode = '';

    #[ORM\Column(name: 'code', type: 'string', length: 128)]
    public string $code = '';
}
