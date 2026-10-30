<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="model",
 *     uniqueConstraints=[
 *         new ORM\UniqueConstraint(['provider_code', 'code'], 'uq_model_provider_code'),
 *     ],
 * )
 */
final class Model
{
    /** @ORM\Id @ORM\Column(type="integer", autoIncrement=true) */
    private ?int $id = null;

    /**
     * Дублируемый код провайдера — индекс, позволяет искать без join.
     * Сохраняется синхронно с provider.
     */
    /** @ORM\Column(name="provider_code", type="string", length=64, index=true) */
    private string $providerCode;

    /** @ORM\Column(name="code", type="string", length=64) */
    private string $code;

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->providerCode = $provider->getCode();
        $this->code = $code;
    }

    /** @ORM\ManyToOne(targetEntity: Provider::class, cascade: ["remove"], onDelete: "CASCADE", fetch="EAGER") */
    private Provider $provider;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setProvider(Provider $provider): self
    {
        $this->provider = $provider;
        $this->providerCode = $provider->getCode();

        return $this;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /** Формат "providerCode/modelCode". */
    public function getFullCode(): string
    {
        return $this->providerCode . '/' . $this->code;
    }
}
