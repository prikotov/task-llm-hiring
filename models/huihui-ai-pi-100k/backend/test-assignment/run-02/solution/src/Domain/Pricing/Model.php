<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера (естественный ключ — (provider, code)).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider', 'code'])]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\TypedIdentifier]
    #[ORM\Column(type: 'bigint')]
    private null|int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    private Provider $provider;

    public function __construct(string $code, Provider $provider)
    {
        $this->code = $code;
        $this->provider = $provider;
    }

    public function getId(): null|int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
