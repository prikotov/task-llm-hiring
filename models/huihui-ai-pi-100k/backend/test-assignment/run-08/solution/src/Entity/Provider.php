<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Billing\OffPeak;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    public string $name = '';

    /** «HH:MM» UTC или null (нет скидки). */
    #[ORM\Column(name: 'off_peak_window_start', type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowStart = null;

    /** «HH:MM» UTC или null. */
    #[ORM\Column(name: 'off_peak_window_end', type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowEnd = null;

    /** Строка-число (bcmath), напр. «0.5»; null — без скидки. */
    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 30, nullable: true)]
    public ?string $offPeakMultiplier = null;

    /** @return null если у провайдера нет оф-пика */
    public function offPeak(): ?OffPeak
    {
        if (
            $this->offPeakWindowStart === null
            || $this->offPeakWindowEnd === null
            || $this->offPeakMultiplier === null
        ) {
            return null;
        }

        return new OffPeak(
            windowStart: $this->offPeakWindowStart,
            windowEnd: $this->offPeakWindowEnd,
            multiplier: $this->offPeakMultiplier,
        );
    }
}
