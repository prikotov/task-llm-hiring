<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\ProviderRepository::class)]
#[ORM\Table(name: 'providers', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'string', length: 128)]
    public string $code;

    #[ORM\Column(type: 'string', length: 255)]
    public string $name;

    #[ORM\Column(name: 'off_peak_start', type: 'string', length: 5, nullable: true)]
    public ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: 'string', length: 5, nullable: true)]
    public ?string $offPeakEnd = null;

    /** Десятичная строка множителя (например «0.5»), float запрещён. */
    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 32, nullable: true)]
    public ?string $offPeakMultiplier = null;

    public function hasOffPeak(): bool
    {
        return $this->offPeakStart !== null
            && $this->offPeakEnd !== null
            && $this->offPeakMultiplier !== null;
    }
}
