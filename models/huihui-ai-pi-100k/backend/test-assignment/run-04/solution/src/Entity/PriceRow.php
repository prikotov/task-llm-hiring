<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows', columns: ['provider_code', 'model_code', 'valid_from'])]
final class PriceRow
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        #[ORM\Column(type: Types::BIGINT)]
        public int $id,

        #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 128)]
        public string $providerCode,

        #[ORM\Column(name: 'model_code', type: Types::STRING, length: 128)]
        public string $modelCode,

        #[ORM\Column(name: 'valid_from', type: Types::DATETIME_UTC_IMMUTABLE)]
        public DateTimeImmutable $validFrom,

        #[ORM\Column(type: Types::STRING, length: 8)]
        public string $currency,

        #[ORM\Column(type: Types::STRING, length: 40)]
        public string $unit,

        #[ORM\Column(name: 'rate_input', type: Types::DECIMAL, nullable: true, options: ['precision' => 20, 'scale' => 16])]
        public ?string $rateInput = null,

        #[ORM\Column(name: 'rate_output', type: Types::DECIMAL, nullable: true, options: ['precision' => 20, 'scale' => 16])]
        public ?string $rateOutput = null,

        #[ORM\Column(name: 'rate_cache_read', type: Types::DECIMAL, nullable: true, options: ['precision' => 20, 'scale' => 16])]
        public ?string $rateCacheRead = null,

        #[ORM\Column(name: 'rate_cache_write', type: Types::DECIMAL, nullable: true, options: ['precision' => 20, 'scale' => 16])]
        public ?string $rateCacheWrite = null,

        #[ORM\Column(name: 'rate_any', type: Types::DECIMAL, nullable: true, options: ['precision' => 20, 'scale' => 16])]
        public ?string $rateAny = null,
    ) {
    }

    /** @return array{unit: string, input: ?string, output: ?string, cache_read: ?string, cache_write: ?string, any: ?string} */
    public function ratesAsArray(): array
    {
        return [
            'unit' => $this->unit,
            'input' => $this->rateInput,
            'output' => $this->rateOutput,
            'cache_read' => $this->rateCacheRead,
            'cache_write' => $this->rateCacheWrite,
            'any' => $this->rateAny,
        ];
    }
}
