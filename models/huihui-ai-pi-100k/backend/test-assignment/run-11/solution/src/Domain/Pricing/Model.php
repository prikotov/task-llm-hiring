<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Естественный ключ — (provider code, model code)
 * (TASK.md: «модели — по `(provider, code)`»).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';
}
