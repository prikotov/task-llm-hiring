<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'model')]
#[ORM\UniqueConstraint('model_prov_code_unique', ['provider_id', 'code'])]
/**
 * Модель LLM у конкретного провайдера. Естественный ключ: (provider_id, code).
 */
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code = '';

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function getId(): ?int
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

    /**
     * Человекочитаемое имя модели: <provider>/<code>.
     */
    public function getModelLabel(): string
    {
        return $this->provider->getCode() . '/' . $this->code;
    }
}
