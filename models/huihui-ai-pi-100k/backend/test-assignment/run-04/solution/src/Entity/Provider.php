<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::STRING, length: 128)]
        public string $code,

        #[ORM\Column(type: Types::STRING, length: 255)]
        public string $name,

        #[ORM\Column(name: 'off_peak_window_start', type: Types::STRING, length: 5, nullable: true)]
        public ?string $offPeakWindowStart = null,

        #[ORM\Column(name: 'off_peak_window_end', type: Types::STRING, length: 5, nullable: true)]
        public ?string $offPeakWindowEnd = null,

        #[ORM\Column(name: 'off_peak_multiplier', type: Types::DECIMAL, nullable: true, options: ['precision' => 20, 'scale' => 16])]
        public ?string $offPeakMultiplier = null,
    ) {
    }
}
