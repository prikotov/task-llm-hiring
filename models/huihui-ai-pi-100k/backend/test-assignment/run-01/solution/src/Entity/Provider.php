<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\OffPeakWindow;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $code;

    #[ORM\Column(type: Types::TEXT)]
    private string $name;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => 'Оф-пик: начало окна, минуты от 00:00 UTC, включительно'])]
    private ?int $offPeakStartMinutes = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => 'Оф-пик: конец окна, минуты от 00:00 UTC, исключительно'])]
    private ?int $offPeakEndMinutes = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => 'Оф-пик: множитель стоимости (десятичная строка)'])]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name, ?OffPeakWindow $offPeak = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->setOffPeak($offPeak);
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setOffPeak(?OffPeakWindow $offPeak): void
    {
        if ($offPeak === null) {
            $this->offPeakStartMinutes = null;
            $this->offPeakEndMinutes = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakStartMinutes = $offPeak->startMinutes();
        $this->offPeakEndMinutes = $offPeak->endMinutes();
        $this->offPeakMultiplier = $offPeak->multiplier();
    }

    public function offPeak(): ?OffPeakWindow
    {
        if (
            $this->offPeakMultiplier === null
            || $this->offPeakStartMinutes === null
            || $this->offPeakEndMinutes === null
        ) {
            return null;
        }

        return OffPeakWindow::fromParts($this->offPeakStartMinutes, $this->offPeakEndMinutes, $this->offPeakMultiplier);
    }
}
