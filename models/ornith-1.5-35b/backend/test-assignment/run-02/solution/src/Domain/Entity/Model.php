<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'model')]
final class Model
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public string $code,

        #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Provider::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(name: 'provider_code', referencedColumnName: 'provider_code', nullable: false)]
        public Provider $dataProvider,
    ) {
    }

    /// Canonical label used in reports, e.g. prism/lumen-8.
    public function displayName(): string
    {
        return $this->dataProvider->providerCode . '/' . $this->code;
    }
}
