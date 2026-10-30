<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $code,

        #[ORM\Column(type: 'string')]
        public readonly string $name,

        #[ORM\Column(type: 'string', nullable: true)]
        public readonly ?string $windowStart,

        #[ORM\Column(type: 'string', nullable: true)]
        public readonly ?string $windowEnd,

        #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
        public readonly ?string $offPeakMultiplier,
    ) {
    }

    /**
     * Попало ли время суток (UTC) в оф-пиковое окно.
     *
     * Окно [windowStart, windowEnd), может переходить через полночь.
     */
    public function isOffPeak(int $hourUtc, int $minuteUtc): bool
    {
        if (null === $this->windowStart || null === $this->windowEnd) {
            return false;
        }

        $start = $this->parseTime($this->windowStart);
        $end = $this->parseTime($this->windowEnd);
        $now = $hourUtc * 60 + $minuteUtc;

        if ($start < $end) {
            return $now >= $start && $now < $end;
        }

        // окно через полночь
        return $now >= $start || $now < $end;
    }

    private function parseTime(string $h): int
    {
        [$hour, $minute] = explode(':', $h);

        return (int) $hour * 60 + (int) $minute;
    }
}
