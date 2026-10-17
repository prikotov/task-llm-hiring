<?php

declare(strict_types=1);

namespace App\Entity;

use App\Billing\OffPeak;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private string $code;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    /**
     * Оф-пик провайдера по UTC хранится в виде колонок:
     * окно [offPeakStart, offPeakEnd), время «HH:MM», множитель — decimal-строка.
     * null-значения — у провайдера нет оф-пик скидки.
     */
    #[ORM\Column(name: 'off_peak_start', type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: Types::STRING, length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakStart !== null
            && $this->offPeakEnd !== null
            && $this->offPeakMultiplier !== null;
    }

    public function getOffPeak(): ?OffPeak
    {
        if (!$this->hasOffPeak()) {
            return null;
        }

        return new OffPeak($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }

    public function replaceOffPeak(?string $start, ?string $end, ?string $multiplier): void
    {
        $this->offPeakStart = $start;
        $this->offPeakEnd = $end;
        $this->offPeakMultiplier = $multiplier;
    }
}
