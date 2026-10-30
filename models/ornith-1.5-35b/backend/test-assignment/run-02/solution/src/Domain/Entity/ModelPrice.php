<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'model_price')]
final class ModelPrice
{
    public const UNIT_PER_M = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public string $provider,

        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public string $model,

        #[ORM\Id]
        #[ORM\Column(type: 'datetime_immutable', name: 'valid_from')]
        public DateTimeImmutable $validFrom,

        #[ORM\Column(type: 'string')]
        public string $currency,

        #[ORM\Column(type: 'string')]
        public string $unit,

        #[ORM\Column(type: 'decimal', nullable: true)]
        public ?string $inputRate = null,

        #[ORM\Column(type: 'decimal', nullable: true)]
        public ?string $outputRate = null,

        #[ORM\Column(type: 'decimal', nullable: true)]
        public ?string $cacheReadRate = null,

        #[ORM\Column(type: 'decimal', nullable: true)]
        public ?string $cacheWriteRate = null,

        #[ORM\Column(type: 'decimal', nullable: true)]
        public ?string $anyRate = null,
    ) {
    }

    /** True when the unified pricing scheme (single rate per any token) is used. */
    public function isUnified(): bool
    {
        return self::UNIT_UNIFIED === $this->unit;
    }

    /** @return static */
    public function copy(): static
    {
        return new static(
            $this->provider,
            $this->model,
            $this->validFrom,
            $this->currency,
            $this->unit,
            $this->inputRate,
            $this->outputRate,
            $this->cacheReadRate,
            $this->cacheWriteRate,
            $this->anyRate,
        );
    }
}
