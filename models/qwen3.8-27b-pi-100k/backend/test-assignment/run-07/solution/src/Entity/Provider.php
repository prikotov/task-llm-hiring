<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $code;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /**
     * Множитель оф-пика — десятичная строка (float для денег запрещён).
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(
        string $code,
        string $name,
        ?string $offPeakWindowStart = null,
        ?string $offPeakWindowEnd = null,
        ?string $offPeakMultiplier = null,
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->offPeakWindowStart = $offPeakWindowStart;
        $this->offPeakWindowEnd = $offPeakWindowEnd;
        $this->offPeakMultiplier = $offPeakMultiplier;
    }

    public function getId(): ?int
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

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Устанавливает окно оф-пика (или снимает его). Изменяет состояние,
     * только если значения реально отличаются — повторная загрузка тех же
     * данных не трогает запись в БД.
     */
    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): void
    {
        if ($windowStart === null || $windowEnd === null || $multiplier === null) {
            if ($this->offPeakWindowStart === null
                && $this->offPeakWindowEnd === null
                && $this->offPeakMultiplier === null) {
                return;
            }

            $this->offPeakWindowStart = null;
            $this->offPeakWindowEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $multiplierChanged = $this->offPeakMultiplier === null
            ? true
            : bccomp($this->offPeakMultiplier, $multiplier, 30) !== 0;

        if ($this->offPeakWindowStart !== $windowStart
            || $this->offPeakWindowEnd !== $windowEnd
            || $multiplierChanged) {
            $this->offPeakWindowStart = $windowStart;
            $this->offPeakWindowEnd = $windowEnd;
            $this->offPeakMultiplier = $multiplier;
        }
    }
}
