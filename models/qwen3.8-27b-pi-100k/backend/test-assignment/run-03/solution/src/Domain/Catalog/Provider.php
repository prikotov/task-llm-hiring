<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM в справочнике цен.
 *
 * Естественный ключ — code. Оф-пиковая скидка — на уровне провайдера
 * и действует для всех его моделей.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'decimal', precision: 24, scale: 16, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function id(): int
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

    /** Окно оф-пика провайдера; null, если скидки нет. */
    public function offPeak(): ?OffPeak
    {
        if ($this->offPeakWindowStart === null || $this->offPeakWindowEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeak(
            $this->offPeakWindowStart,
            $this->offPeakWindowEnd,
            $this->offPeakMultiplier,
        );
    }

    /** null снимает оф-пиковое окно. */
    public function setOffPeak(?OffPeak $offPeak): void
    {
        if ($offPeak === null) {
            $this->offPeakWindowStart = null;
            $this->offPeakWindowEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakWindowStart = $offPeak->windowStart;
        $this->offPeakWindowEnd = $offPeak->windowEnd;
        $this->offPeakMultiplier = $offPeak->multiplier();
    }
}
