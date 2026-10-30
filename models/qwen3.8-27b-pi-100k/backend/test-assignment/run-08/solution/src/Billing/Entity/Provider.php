<?php

declare(strict_types=1);

namespace App\Billing\Entity;

use App\Billing\Import\Dto\ProviderInput;
use App\Billing\ValueObject\Decimal;
use App\Billing\ValueObject\OffPeak;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM со (опциональным) оф-пиковым окном UTC.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code;

    #[ORM\Column(type: 'string', length: 255)]
    public string $name = '';

    /** Минуты от полуночи UTC, включительно. */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $offPeakWindowStart = null;

    /** Минуты от полуночи UTC, исключительно. */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $offPeakWindowEnd = null;

    /** Множитель стоимости события в оф-пик, десятичная строка (bcmath). */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    public ?string $offPeakMultiplier = null;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function apply(ProviderInput $input): void
    {
        $this->name = $input->name;
        if ($input->offPeak === null) {
            $this->offPeakWindowStart = null;
            $this->offPeakWindowEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }
        $this->offPeakWindowStart = $input->offPeak->windowStartMinutes;
        $this->offPeakWindowEnd = $input->offPeak->windowEndMinutes;
        $this->offPeakMultiplier = $input->offPeak->multiplier->value();
    }

    public function offPeak(): ?OffPeak
    {
        if ($this->offPeakWindowStart === null || $this->offPeakWindowEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeak(
            Decimal::of($this->offPeakMultiplier),
            $this->offPeakWindowStart,
            $this->offPeakWindowEnd,
        );
    }
}
