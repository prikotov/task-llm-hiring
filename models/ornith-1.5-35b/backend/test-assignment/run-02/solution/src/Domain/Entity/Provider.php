<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider')]
final class Provider
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public string $providerCode,

        #[ORM\Column(type: 'string')]
        public string $name,

        /// Start of the off-peak window in HH:MM (UTC, inclusive). Null when off-peak disabled.
        #[ORM\Column(type: 'string', nullable: true)]
        public ?string $offPeakStart = null,

        /// End of the off-peak window in HH:MM (UTC, exclusive). Null when off-peak disabled.
        #[ORM\Column(type: 'string', nullable: true)]
        public ?string $offPeakEnd = null,

        /// Multiplier applied to event cost inside the off-peak window. Null when disabled.
        #[ORM\Column(type: 'decimal', nullable: true)]
        public ?string $offPeakMultiplier = null,
    ) {
    }
}
