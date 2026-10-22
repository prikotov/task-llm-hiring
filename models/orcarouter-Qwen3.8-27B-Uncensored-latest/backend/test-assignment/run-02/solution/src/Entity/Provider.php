<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /** Время начала оф-пикового окна UTC ("HH:MM"), включительно. */
    #[ORM\Column(name: 'off_peak_window_start', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    /** Время конца оф-пикового окна UTC ("HH:MM"), исключительно. */
    #[ORM\Column(name: 'off_peak_window_end', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /** Множитель стоимости в оф-пик (десятичная строка, напр. "0.5"). */
    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 32, nullable: true)]
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

    public function setOffPeak(?string $start, ?string $end, ?string $multiplier): void
    {
        $this->offPeakWindowStart = $start;
        $this->offPeakWindowEnd = $end;
        $this->offPeakMultiplier = $multiplier;
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
}
