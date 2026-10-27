<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Натуральный ключ — `(provider, code)`.
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
final class Model
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $providerCode = '';

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $code = '';
}
