<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM (справочник).
 * Оф-пик: минуты от начала суток UTC + десятичный множитель; nullable — без скидки.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 64, nullable: false)]
    private string $code = '';

    #[ORM\Column(length: 255, nullable: false)]
    private string $name = '';

    #[ORM\Column(name: 'off_peak_window_start', type: Types::INTEGER, nullable: true)]
    private ?int $offPeakWindowStart = null;

    #[ORM\Column(name: 'off_peak_window_end', type: Types::INTEGER, nullable: true)]
    private ?int $offPeakWindowEnd = null;

    /** Десятичная строка, 6 знаков (запас: до 16 при сохранении). */
    #[ORM\Column(name: 'off_peak_multiplier', type: Types::TEXT, nullable: true)]
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakWindowStart !== null
            && $this->offPeakWindowEnd !== null
            && $this->offPeakMultiplier !== null;
    }

    public function offPeakStart(): int
    {
        return $this->offPeakWindowStart;
    }

    public function offPeakEnd(): int
    {
        return $this->offPeakWindowEnd;
    }

    public function offPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    public function setOffPeak(?int $startMin, ?int $endMin, ?string $multiplier): void
    {
        $this->clearOffPeak();

        if ($startMin === null || $endMin === null || $multiplier === null) {
            return;
        }

        $this->applyOffPeak($startMin, $endMin, $multiplier);
    }

    public function clearOffPeak(): void
    {
        $this->offPeakWindowStart = null;
        $this->offPeakWindowEnd = null;
        $this->offPeakMultiplier = null;
    }

    private function applyOffPeak(int $startMin, int $endMin, string $multiplier): void
    {
        if ($startMin < 0 || $startMin >= 1440 || $endMin < 0 || $endMin >= 1440) {
            throw new \DomainException(
                sprintf('Оф-пик: минуты вне диапазона суток (0..1439) — start=%d end=%d', $startMin, $endMin)
            );
        }
        if (!preg_match('/^\d+(\.\d+)?$/', $multiplier)) {
            throw new \DomainException(sprintf('Оф-пик: множитель должен быть десятичным числом, получено: %s', $multiplier));
        }

        $this->offPeakWindowStart = $startMin;
        $this->offPeakWindowEnd = $endMin;
        $this->offPeakMultiplier = $multiplier;
    }
}
