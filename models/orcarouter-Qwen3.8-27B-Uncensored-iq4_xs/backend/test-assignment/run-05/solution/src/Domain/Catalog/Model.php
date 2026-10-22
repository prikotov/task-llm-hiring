<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $provider = '';

    #[ORM\Column(type: 'string', length: 128)]
    private string $code = '';

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function code(): string
    {
        return $this->code;
    }

    /** Отображаемое имя модели: "<provider>/<code>". */
    public function fullName(): string
    {
        return $this->provider.'/'.$this->code;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
