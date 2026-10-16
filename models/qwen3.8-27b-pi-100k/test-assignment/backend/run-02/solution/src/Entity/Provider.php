<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Billing\OffPeakPolicy;
use App\Repository\ProviderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM: справочник + параметры оф-пик скидки.
 * Естественный ключ — code.
 */
#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $code;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start', type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: Types::STRING, length: 40, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
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

    public function offPeakStart(): ?string
    {
        return $this->offPeakStart;
    }

    public function offPeakEnd(): ?string
    {
        return $this->offPeakEnd;
    }

    public function offPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Установка (или снятие) оф-пик окна; все три поля задаются вместе —
     * окно без множителя не существует.
     */
    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): void
    {
        $this->offPeakStart = $windowStart;
        $this->offPeakEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;
    }

    /** Оф-пик скидка как предметная модель; null — скидки нет. */
    public function offPeakPolicy(): ?OffPeakPolicy
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeakPolicy($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }
}
