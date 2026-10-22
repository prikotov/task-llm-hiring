<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'catalog_models')]
#[ORM\UniqueConstraint(name: 'uniq_catalog_models_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Column(type: 'string', length: 128)]
    public string $code = '';
}
