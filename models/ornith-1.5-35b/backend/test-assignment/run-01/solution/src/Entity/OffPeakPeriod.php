<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Decimal;
use App\Domain\Instant;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'off_peak_period')]
/**
 * Схема оф-пиковой скидки провайдера.
 * Связана OneToOne с провайдером; отсутствие записи = скидки нет.
 * Окно времени суток UTC: [window_start, window_end).
 */
final class OffPeakPeriod
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'offPeakPeriod')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', cascade: ['persist'])]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 8)]
    private string $windowStart;

    #[ORM\Column(type: 'string', length: 8)]
    private string $windowEnd;

    #[ORM\Column(type: 'string', length: 32)]
    private string $discountMultiplier;

    public function __construct(
        Provider $provider,
        string $windowStart,
        string $windowEnd,
        string $discountMultiplier
    ) {
        $this->provider = $provider;
        $this->windowStart = $windowStart;
        $this->windowEnd = $windowEnd;
        $this->discountMultiplier = $discountMultiplier;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getWindowStart(): Instant
    {
        return Instant::fromString($this->windowStart);
    }

    public function getWindowEnd(): Instant
    {
        return Instant::fromString($this->windowEnd);
    }

    public function getDiscountMultiplier(): Decimal
    {
        return Decimal::fromString($this->discountMultiplier);
    }

    public function isAt(Instant $instant): bool
    {
        $start = $this->secondsOfDay($this->windowStart);
        $end = $this->secondsOfDay($this->windowEnd);
        $s = $instant->secondsOfDay();
        if ($start < $end) {
            return $s >= $start && $s < $end;
        }
        // окно через полночь
        return $s >= $start || $s < $end;
    }

    private function secondsOfDay(string $hhmm): int
    {
        [$h, $m] = explode(':', $hhmm);
        return (int) $h * 3600 + (int) $m * 60;
    }
}
