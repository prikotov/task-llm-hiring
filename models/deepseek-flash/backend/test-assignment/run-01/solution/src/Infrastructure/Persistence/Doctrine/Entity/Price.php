<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Money\Decimal;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\RateCard;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uniq_prices_model_valid_from', columns: ['provider_model_id', 'valid_from'])]
final class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProviderModel::class)]
    #[ORM\JoinColumn(name: 'provider_model_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ProviderModel $model;

    #[ORM\Column(name: 'valid_from', type: 'datetimetz_immutable', precision: 6)]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32, enumType: PriceUnit::class)]
    private PriceUnit $unit;

    #[ORM\Column(name: 'rate_input', type: 'decimal', precision: 38, scale: 18, nullable: true)]
    private ?string $rateInput;

    #[ORM\Column(name: 'rate_output', type: 'decimal', precision: 38, scale: 18, nullable: true)]
    private ?string $rateOutput;

    #[ORM\Column(name: 'rate_cache_read', type: 'decimal', precision: 38, scale: 18, nullable: true)]
    private ?string $rateCacheRead;

    #[ORM\Column(name: 'rate_cache_write', type: 'decimal', precision: 38, scale: 18, nullable: true)]
    private ?string $rateCacheWrite;

    #[ORM\Column(name: 'rate_any', type: 'decimal', precision: 38, scale: 18, nullable: true)]
    private ?string $rateAny;

    public function __construct(
        ProviderModel $model,
        DateTimeImmutable $validFrom,
        string $currency,
        PriceUnit $unit,
        ?string $rateInput,
        ?string $rateOutput,
        ?string $rateCacheRead,
        ?string $rateCacheWrite,
        ?string $rateAny,
    ) {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rateInput = $rateInput;
        $this->rateOutput = $rateOutput;
        $this->rateCacheRead = $rateCacheRead;
        $this->rateCacheWrite = $rateCacheWrite;
        $this->rateAny = $rateAny;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function model(): ProviderModel
    {
        return $this->model;
    }

    public function validFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function unit(): PriceUnit
    {
        return $this->unit;
    }

    public function rateCard(): RateCard
    {
        return match ($this->unit) {
            PriceUnit::PerMillionTokens => RateCard::perToken(
                Decimal::fromString($this->requireRate($this->rateInput)),
                Decimal::fromString($this->requireRate($this->rateOutput)),
                Decimal::fromString($this->requireRate($this->rateCacheRead)),
                Decimal::fromString($this->requireRate($this->rateCacheWrite)),
            ),
            PriceUnit::PerMillionTokensUnified => RateCard::unified(
                Decimal::fromString($this->requireRate($this->rateAny)),
            ),
        };
    }

    public function update(
        string $currency,
        PriceUnit $unit,
        ?string $rateInput,
        ?string $rateOutput,
        ?string $rateCacheRead,
        ?string $rateCacheWrite,
        ?string $rateAny,
    ): bool {
        $changed = false;

        if ($this->currency !== $currency) {
            $this->currency = $currency;
            $changed = true;
        }

        if ($this->unit !== $unit) {
            $this->unit = $unit;
            $changed = true;
        }

        if (!$this->sameDecimal($this->rateInput, $rateInput)) {
            $this->rateInput = $rateInput;
            $changed = true;
        }

        if (!$this->sameDecimal($this->rateOutput, $rateOutput)) {
            $this->rateOutput = $rateOutput;
            $changed = true;
        }

        if (!$this->sameDecimal($this->rateCacheRead, $rateCacheRead)) {
            $this->rateCacheRead = $rateCacheRead;
            $changed = true;
        }

        if (!$this->sameDecimal($this->rateCacheWrite, $rateCacheWrite)) {
            $this->rateCacheWrite = $rateCacheWrite;
            $changed = true;
        }

        if (!$this->sameDecimal($this->rateAny, $rateAny)) {
            $this->rateAny = $rateAny;
            $changed = true;
        }

        return $changed;
    }

    private function sameDecimal(?string $left, ?string $right): bool
    {
        if ($left === null || $right === null) {
            return $left === $right;
        }

        return Decimal::fromString($left)->equals(Decimal::fromString($right));
    }

    private function requireRate(?string $rate): string
    {
        if ($rate === null) {
            throw new \LogicException(sprintf(
                'Price #%s has no rate for unit "%s".',
                $this->id ?? 'new',
                $this->unit->value,
            ));
        }

        return $rate;
    }
}
