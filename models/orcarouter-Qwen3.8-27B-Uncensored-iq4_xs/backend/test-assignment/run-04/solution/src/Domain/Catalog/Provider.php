<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM. Естественный ключ — code.
 */
#[ORMntity]
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $name;

    /** "HH:MM" UTC, null — окна оф-пик нет (все три параметра вместе). */
    #[ORM\Column(name: 'off_peak_window_start', type: 'string', nullable: true)]
    private ?string $offPeakWindowStart;

    #[ORM\Column(name: 'off_peak_window_end', type: 'string', nullable: true)]
    private ?string $offPeakWindowEnd;

    /** Десятичная строка множителя (float запрещён). */
    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', nullable: true)]
    private ?string $offPeakMultiplier;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function apply(string $name, ?string $offPeakWindowStart, ?string $offPeakWindowEnd, ?string $offPeakMultiplier): void
    {
        $this->name = $name;
        $this->offPeakWindowStart = $offPeakWindowStart;
        $this->offPeakWindowEnd = $offPeakWindowEnd;
        $this->offPeakMultiplier = $offPeakMultiplier;
    }

    /**
     * Окно оф-пиковой скидки или null, если у провайдера скидки нет.
     */
    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakWindowStart === null || $this->offPeakWindowEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeakWindow($this->offPeakWindowStart, $this->offPeakWindowEnd, $this->offPeakMultiplier);
    }
}
