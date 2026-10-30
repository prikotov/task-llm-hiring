<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Money\Money;

/**
 * @ORM\Entity
 */
final class Provider
{
    /**
     * @ORM\Id
     * @ORM\Column(type: "string", length: 64, unique=true)
     */
    private string $code;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $name;

    /**
     * @ORM\Column(type: "string", length: 8) nullable=true
     */
    private ?string $offPeakWindowStart = null;

    /**
     * @ORM\Column(type: "string", length: 8) nullable=true
     */
    private ?string $offPeakWindowEnd = null;

    /**
     * @ORM\Column(type: "string", length: 64) nullable=true
     * Множитель стоимости (string, 6+ знаков) — NULL = нет скидки.
     */
    private ?string $offPeakMultiplier = null;

    /**
     * @ORM\ManyToOne
     * @ORM\JoinColumn(name: "provider_code", target_entity: "App\Entity\Provider",
     *     referenced_column_name: "code", onDelete: "CASCADE")
     */
    private array $models;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->models = [];
    }

    public function setOffPeak(?array $offPeak): void
    {
        if ($offPeak === null) {
            $this->offPeakWindowStart = null;
            $this->offPeakWindowEnd = null;
            $this->offPeakMultiplier = null;
            return;
        }
        $this->offPeakWindowStart = (string) $offPeak['window_start'];
        $this->offPeakWindowEnd = (string) $offPeak['window_end'];
        $this->offPeakMultiplier = (string) $offPeak['multiplier'];
    }

    public function setModels(array $models): void
    {
        $this->models = $models;
    }

    public function getModels(): array
    {
        return $this->models;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOffPeak(): ?array
    {
        if ($this->offPeakMultiplier === null || $this->offPeakWindowStart === null || $this->offPeakWindowEnd === null) {
            return null;
        }
        return [
            'window_start' => $this->offPeakWindowStart,
            'window_end' => $this->offPeakWindowEnd,
            'multiplier' => $this->offPeakMultiplier,
        ];
    }
}
