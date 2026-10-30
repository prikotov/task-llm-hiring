<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\ModelRepository::class)]
#[ORM\Table(name: 'models', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider', 'code'])])]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'string', length: 128)]
    public string $provider;

    #[ORM\Column(type: 'string', length: 128)]
    public string $code;
}
