<?php

declare(strict_types=1);

namespace App\Billing\Entity;

#[\Doctrine\ORM\Mapping\Entity]
#[\Doctrine\ORM\Mapping\Table(name: 'models')]
#[\Doctrine\ORM\Mapping\UniqueConstraint(name: 'uniq_model', columns: ['provider_code', 'code'])]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_model_provider', columns: ['provider_code'])]
class Model
{
    /** Пустая string; <providerCode>\u0001<code>. */
    public function __construct(
        #[\Doctrine\ORM\Mapping\Id]
        #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 128)]
        #[\Doctrine\ORM\Mapping\GeneratedValue(strategy: 'NONE')]
        public readonly string $id,
    ) {}

    #[\Doctrine\ORM\Mapping\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 128)]
    public string $code = '';
}
