<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Money;
use App\Domain\OffPeakWindow;
use App\Repository\ProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/** Провайдер LLM и параметры его оф-пикового окна (UTC). */
#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $code;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start_min', type: Types::INTEGER, nullable: true)]
    private ?int $offPeakStartMinute = null;

    #[ORM\Column(name: 'off_peak_end_min', type: Types::INTEGER, nullable: true)]
    private ?int $offPeakEndMinute = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: Types::STRING, length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Применяет окно оф-пика (все три значения одновременно или все null).
     * Возвращает true, если хранимые значения изменились.
     */
    public function applyOffPeak(?int $startMinute, ?int $endMinute, ?string $multiplier): bool
    {
        $same = $this->offPeakStartMinute === $startMinute
            && $this->offPeakEndMinute === $endMinute
            && $this->multiplierEquals($this->offPeakMultiplier, $multiplier);

        if ($same) {
            return false;
        }

        $this->offPeakStartMinute = $startMinute;
        $this->offPeakEndMinute = $endMinute;
        $this->offPeakMultiplier = $multiplier;

        return true;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakMultiplier !== null;
    }

    public function offPeakWindow(): ?OffPeakWindow
    {
        if ($this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeakWindow($this->offPeakStartMinute ?? 0, $this->offPeakEndMinute ?? 0, $this->offPeakMultiplier);
    }

    private function multiplierEquals(?string $a, ?string $b): bool
    {
        if ($a === null || $b === null) {
            return $a === $b;
        }

        return Money::cmp($a, $b) === 0;
    }
}
