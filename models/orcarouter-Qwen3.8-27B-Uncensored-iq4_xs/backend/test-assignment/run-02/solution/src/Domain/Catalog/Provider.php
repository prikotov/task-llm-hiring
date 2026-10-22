<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\Pricing\OffPeakWindow;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    /** Минут от полуночи, включительно. null — окна оф-пика нет. */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offPeakWindowStart = null;

    /** Минут от полуночи, эксклюзивно. */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offPeakWindowEnd = null;

    /** Десятичная строка (bcmath), например "0.5". */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setOffPeak(?int $windowStartMin, ?int $windowEndMin, ?string $multiplier): void
    {
        $this->offPeakWindowStart = $windowStartMin;
        $this->offPeakWindowEnd = $windowEndMin;
        $this->offPeakMultiplier = $multiplier;
    }

    /** Окно оф-пика провайдера; null — скидки нет. */
    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakWindowStart === null
            || $this->offPeakWindowEnd === null
            || $this->offPeakMultiplier === null
        ) {
            return null;
        }

        return new OffPeakWindow($this->offPeakWindowStart, $this->offPeakWindowEnd, $this->offPeakMultiplier);
    }
}
