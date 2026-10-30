<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\Index(name: 'model_uid', columns: ['provider_code', 'code'])]
class ModelEntity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $providerCode,

        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $code,

        #[ORM\Column(type: 'string')]
        public readonly string $name,
    ) {
    }
}
