<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_model_provider_code', columns: ['provider', 'code'])]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    public int|null $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $provider = '';

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $code = '';
}
