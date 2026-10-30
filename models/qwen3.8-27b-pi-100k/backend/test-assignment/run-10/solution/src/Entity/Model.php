<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Естественный ключ — (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\Index(name: 'idx_model_provider', columns: ['provider_id'])]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider_id', 'code'])]
class Model extends AbstractEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
        #[ORM\JoinColumn(nullable: false)]
        private Provider $provider,
        #[ORM\Column(type: 'string', length: 64)]
        private string $code,
    ) {
        $this->prices = new ArrayCollection();
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
     * Метка модели в отчёте — `<provider>/<code>`.
     */
    public function getLabel(): string
    {
        return $this->provider->getCode() . '/' . $this->code;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * @var Collection<int, Price>
     */
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Price::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $prices;
}
