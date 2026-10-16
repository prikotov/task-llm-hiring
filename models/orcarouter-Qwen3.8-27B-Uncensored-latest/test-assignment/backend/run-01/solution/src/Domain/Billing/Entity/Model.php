<?php

declare(strict_types=1);

namespace App\Domain\Billing\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Натуральный ключ — (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';

    public function __construct(string $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }
}
