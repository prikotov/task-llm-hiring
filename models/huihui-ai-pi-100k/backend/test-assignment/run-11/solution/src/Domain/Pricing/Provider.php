<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Billing\Cost;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер (справочник). Код — естественный ключ, upsert по нему
 * (TASK.md: «провайдеры — по `code`»).
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

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    public ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    public ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    public ?string $offPeakMultiplier = null;

    public static function applyTo(
        Provider $provider,
        string $code,
        ?object $offPeakPolicy,
    ): self {
        $provider->code = $code;
        if ($offPeakPolicy === null) {
            $provider->offPeakWindowStart = null;
            $provider->offPeakWindowEnd = null;
            $provider->offPeakMultiplier = null;
        } else {
            $provider->offPeakWindowStart = $offPeakPolicy->windowStart;
            $provider->offPeakWindowEnd = $offPeakPolicy->windowEnd;
            $provider->offPeakMultiplier = $offPeakPolicy->multiplier->toSixDecimalsTruncated();
        }

        return $provider;
    }

    public function offPeak(): ?OffPeakPolicy
    {
        if ($this->offPeakWindowStart === null) {
            return null;
        }

        return new OffPeakPolicy(
            windowStart: $this->offPeakWindowStart,
            windowEnd: (string) $this->offPeakWindowEnd,
            multiplier: Cost::make((string) $this->offPeakMultiplier),
        );
    }
}
