<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'model')]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 255)]
    private string $code = '';

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
