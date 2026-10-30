<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель LLM внутри провайдера. Естественный ключ — (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: 'string', nullable: false)]
    private string $providerCode;

    #[ORM\Column(type: 'string')]
    private string $code;

    /**
     * @var Collection<int, PriceRow>
     */
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: PriceRow::class)]
    private Collection $priceRows;

    public function __construct(string $providerCode, string $code)
    {
        $this->priceRows = new ArrayCollection();
        $this->providerCode = $providerCode;
        $this->code = $code;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function code(): string
    {
        return $this->code;
    }

    /**
     * Имя модели в отчётах: <provider>/<code>.
     */
    public function qualifiedName(): string
    {
        return $this->providerCode.'/'.$this->code;
    }

    public function apply(string $code): void
    {
        $this->code = $code;
    }
}
