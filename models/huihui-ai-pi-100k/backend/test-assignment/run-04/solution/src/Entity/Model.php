<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider_code', 'code'])]
final class Model
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        #[ORM\Column(type: Types::BIGINT)]
        public int $id,

        #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 128)]
        public string $providerCode,

        #[ORM\Column(type: Types::STRING, length: 128)]
        public string $code,
    ) {
    }
}
