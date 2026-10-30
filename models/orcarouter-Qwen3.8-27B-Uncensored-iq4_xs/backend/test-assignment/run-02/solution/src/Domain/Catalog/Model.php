<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'provider_id', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code = '';

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function code(): string
    {
        return $this->code;
    }

    /** Идентификатор модели в отчётах: "<provider>/<code>". */
    public function label(): string
    {
        return $this->provider->code().'/'.$this->code;
    }
}
