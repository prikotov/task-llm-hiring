<?php

declare(strict_types=1);

namespace App\Domain\Billing\Entity;

use App\Domain\Billing\Pricing\OffPeak;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM. Натуральный ключ — code. off_peak хранится вложенным
 * (окно по времени суток UTC и множитель стоимости).
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    public string $name = '';

    /** null — у провайдера нет оф-пиковой скидки. */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    public ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    public ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /** @param array{window_start: string, window_end: string, multiplier: string}|null $offPeak */
    public function setOffPeak(?array $offPeak): void
    {
        if ($offPeak === null) {
            $this->offPeakWindowStart = null;
            $this->offPeakWindowEnd = null;
            $this->offPeakMultiplier = null;
            return;
        }

        $vo = OffPeak::fromArray($offPeak);
        $this->offPeakWindowStart = $vo->windowStart;
        $this->offPeakWindowEnd = $vo->windowEnd;
        $this->offPeakMultiplier = $vo->multiplier;
    }

    public function getOffPeak(): ?OffPeak
    {
        if ($this->offPeakWindowStart === null) {
            return null;
        }

        return new OffPeak(
            $this->offPeakWindowStart,
            $this->offPeakWindowEnd ?? $this->offPeakWindowStart,
            $this->offPeakMultiplier ?? '1'
        );
    }
}
