<?php

declare(strict_types=1);

namespace App\Persistence\Entity;

use App\Domain\Pricing\OffPeakWindow;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    #[ORM\Column(type: 'text')]
    public string $code = '';

    #[ORM\Column(type: 'text')]
    public string $name = '';

    #[ORM\Column(name: 'off_peak_start', type: 'integer', nullable: true)]
    public ?int $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: 'integer', nullable: true)]
    public ?int $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'text', nullable: true)]
    public ?string $offPeakMultiplier = null;

    /** Оф-пиковое окно провайдера; null — у провайдера скидка не настроена. */
    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeakWindow($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }
}
