<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Природный ключ — (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider', 'code'])]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    public int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $provider = '';

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $code = '';
}
