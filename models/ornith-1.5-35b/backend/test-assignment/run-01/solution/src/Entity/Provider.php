<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider')]
#[ORM\UniqueConstraint('provider_code_unique', ['code'])]
/**
 * Провайдер доступа к LLM-моделям.
 */
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\OneToOne(targetEntity: OffPeakPeriod::class, mappedBy: 'provider', cascade: ['persist'], orphanRemoval: true)]
    private ?OffPeakPeriod $offPeakPeriod = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OffPeakPeriod|null
     */
    public function getOffPeakPeriod(): ?OffPeakPeriod
    {
        return $this->offPeakPeriod;
    }
}
