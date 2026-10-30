<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\ValueObject\OffPeakWindow;
use App\Repository\ProviderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(length: 64)]
    private string $code;

    #[ORM\Column(length: 128)]
    private string $name;

    /** OffPeakWindow | null (persisted as JSON). */
    #[ORM\Column(type: 'json')]
    private ?array $offPeakSerialized = null;

    private ?OffPeakWindow $offPeak = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->offPeak = null;
        $this->offPeakSerialized = null;
    }

    public function setOffPeak(?OffPeakWindow $offPeak): void
    {
        $this->offPeak = $offPeak;
        $this->offPeakSerialized = $offPeak === null ? null : [
            'start_minutes' => $offPeak->startMinutesForSerialization(),
            'end_minutes' => $offPeak->endMinutesForSerialization(),
            'multiplier' => $offPeak->multiplier(),
        ];
    }

    public function offPeak(): ?OffPeakWindow
    {
        return $this->offPeak;
    }

    public function offPeakFromStorage(): ?OffPeakWindow
    {
        if ($this->offPeakSerialized === null) {
            return null;
        }
        $s = $this->offPeakSerialized['start_minutes'] ?? null;
        $e = $this->offPeakSerialized['end_minutes'] ?? null;
        $m = $this->offPeakSerialized['multiplier'] ?? null;
        if ($s === null || $e === null || $m === null) {
            return null;
        }
        return new OffPeakWindow((int) $s, (int) $e, (string) $m);
    }

    public function code(): string
    {
        return $this->code;
    }
}
