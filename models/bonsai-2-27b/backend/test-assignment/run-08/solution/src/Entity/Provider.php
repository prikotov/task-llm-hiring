<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="provider")
 */
final class Provider
{
    /** @ORM\Column(name="code", type="string", length=64, unique=true, index=true) */
    private string $code;

    /** @ORM\Column(name="name", type="string", length=255) */
    private string $name;

    /**
     * Оф-пиковая скидка: окно [window_start, window_end) (HH:MM, UTC),
     * окно может переходить через полночь. multiplier — строка, десятичная.
     * null — скидки нет.
     */
    /** @ORM\Column(name="off_peak_window_start", type="string", length=5, nullable=true) */
    private ?string $offPeakWindowStart = null;

    /** @ORM\Column(name="off_peak_window_end", type="string", length=5, nullable=true) */
    private ?string $offPeakWindowEnd = null;

    /** @ORM\Column(name="off_peak_multiplier", type="string", length=32, nullable=true) */
    private ?string $offPeakMultiplier = null;

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setCodeFromRaw(string $code): self
    {
        $this->code = trim($code);

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setOffPeak(?OffPeak $offPeak): self
    {
        $this->offPeakWindowStart = $offPeak->start;
        $this->offPeakWindowEnd = $offPeak->end;
        $this->offPeakMultiplier = $offPeak->multiplier;

        return $this;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakWindowStart !== null
            && $this->offPeakWindowEnd !== null
            && $this->offPeakMultiplier !== null;
    }

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    /** Множитель скидки в точном формате bcmath, или null. */
    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /**
     * Падает ли момент времени суток (HH:MM UTC) в окно оф-пик
     * [window_start, window_end). Окно может переходить через полночь.
     */
    public function isOffPeak(string $hourMinute): bool
    {
        if (!$this->hasOffPeak()) {
            return false;
        }

        $start = $this->offPeakWindowStart;
        $end = $this->offPeakWindowEnd;

        if ($start === $end) {
            return $hourMinute === $start;
        }

        if ($start < $end) {
            return $hourMinute >= $start && $hourMinute < $end;
        }

        // Окно через полночь: [16:30, 00:30)
        return $hourMinute >= $start || $hourMinute < $end;
    }
}
