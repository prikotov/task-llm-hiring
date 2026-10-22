<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Pricing\PriceEntry;
use Doctrine\ORM\Mapping as ORM;

/**
 * One pricing row of a model, valid from a point in time.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_provider_model_valid_from', columns: ['provider', 'model', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_lookup', columns: ['provider', 'model', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    public function __construct(
        #[ORM\Column(type: 'string', length: 64)]
        private string $provider,

        #[ORM\Column(type: 'string', length: 64)]
        private string $model,

        #[ORM\Column(type: 'datetimetz_immutable')]
        private \DateTimeImmutable $validFrom,

        #[ORM\Column(type: 'string', length: 16)]
        private string $currency = 'USD',

        #[ORM\Column(type: 'string', length: 32)]
        private string $unit,

        #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
        private ?string $rateInput = null,

        #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
        private ?string $rateOutput = null,

        #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
        private ?string $rateCacheRead = null,

        #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
        private ?string $rateCacheWrite = null,

        #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
        private ?string $rateAny = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    /** @return array<string, string> */
    public function getRates(): array
    {
        $rates = [];
        foreach ([
            'input' => $this->rateInput,
            'output' => $this->rateOutput,
            'cache_read' => $this->rateCacheRead,
            'cache_write' => $this->rateCacheWrite,
            'any' => $this->rateAny,
        ] as $key => $value) {
            if ($value !== null) {
                $rates[$key] = $value;
            }
        }

        return $rates;
    }

    public function toPriceEntry(): PriceEntry
    {
        $entry = new PriceEntry($this->unit, $this->getRates());

        return $entry;
    }

    /** Apply the data of another price row onto this one (upsert update). */
    public function applyRates(string $currency, string $unit, ?string $rateInput, ?string $rateOutput, ?string $rateCacheRead, ?string $rateCacheWrite, ?string $rateAny): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rateInput = $rateInput;
        $this->rateOutput = $rateOutput;
        $this->rateCacheRead = $rateCacheRead;
        $this->rateCacheWrite = $rateCacheWrite;
        $this->rateAny = $rateAny;
    }
}
