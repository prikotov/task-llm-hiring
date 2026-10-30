<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider_models')]
class ProviderModel
{
    #[ORM\Id]
    #[ORM\Column(name: 'provider_code', type: 'string', length: 120)]
    public string $providerCode;

    #[ORM\Id]
    #[ORM\Column(name: 'code', type: 'string', length: 120)]
    public string $code;
}
