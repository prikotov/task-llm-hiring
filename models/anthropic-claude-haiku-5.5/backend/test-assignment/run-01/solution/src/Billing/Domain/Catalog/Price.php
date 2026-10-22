<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

use App\Billing\Domain\Shared\Decimal;
use App\Billing\Domain\Usage\TokenUsage;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели, действующая с valid_from (включительно).
 */
#[ORM\Entity(repositoryClass: \App\Billing\Infrastructure\Persistence\PriceRepository::class)]
#[ORM\Table(name: 'billing_price')]
#[ORM\UniqueConstraint(name: 'uniq_billing_price_model_valid_from', columns: ['model_id', 'valid_from'])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', nullable: false)]
    private Model $model;

    #[ORM\Column(name: 'valid_from', type: 'datetimetz_immutable', precision: 6)]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(name: 'unit', enumType: PricingUnit::class)]
    private PricingUnit $unit;

    #[ORM\Column(name: 'rate_input', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateInput = null;

    #[ORM\Column(name: 'rate_output', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateOutput = null;

    #[ORM\Column(name: 'rate_cache_read', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateCacheRead = null;

    #[ORM\Column(name: 'rate_cache_write', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateCacheWrite = null;

    #[ORM\Column(name: 'rate_any', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateAny = null;

    /**
     * @param array<string, string> $rates ключи — из PricingUnit::requiredRateKeys()
     */
    public function __construct(
        Model $model,
        DateTimeImmutable $validFrom,
        string $currency,
        PricingUnit $unit,
        array $rates,
    ) {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->changeRates($unit, $rates);
    }

    /** @param array<string, string> $rates */
    public function changeRates(PricingUnit $unit, array $rates): void
    {
        $this->unit = $unit;
        $this->rateInput = $this->rateOutput = $this->rateCacheRead = $this->rateCacheWrite = $this->rateAny = null;

        if ($unit === PricingUnit::PER_1M_TOKENS) {
            $this->rateInput = $rates['input'];
            $this->rateOutput = $rates['output'];
            $this->rateCacheRead = $rates['cache_read'];
            $this->rateCacheWrite = $rates['cache_write'];
        } else {
            $this->rateAny = $rates['any'];
        }
    }

    /** @return array<string, string> */
    public function rates(): array
    {
        return $this->unit === PricingUnit::PER_1M_TOKENS
            ? [
                'input' => (string) $this->rateInput,
                'output' => (string) $this->rateOutput,
                'cache_read' => (string) $this->rateCacheRead,
                'cache_write' => (string) $this->rateCacheWrite,
            ]
            : ['any' => (string) $this->rateAny];
    }

    public function hasSameRates(PricingUnit $unit, array $rates): bool
    {
        if ($this->unit !== $unit) {
            return false;
        }

        foreach ($this->rates() as $key => $value) {
            if (Decimal::compare($value, $rates[$key], Decimal::MONEY_SCALE) !== 0) {
                return false;
            }
        }

        return true;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function validFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    /**
     * Стоимость события по этой строке цен, округлённая до 6 знаков (HALF_UP).
     * Оф-пик берётся у провайдера модели.
     */
    public function charge(TokenUsage $usage, DateTimeImmutable $ts): string
    {
        if ($this->unit === PricingUnit::PER_1M_TOKENS) {
            $sum = Decimal::add(
                Decimal::add(
                    Decimal::mul((string) $usage->input, (string) $this->rateInput),
                    Decimal::mul((string) $usage->output, (string) $this->rateOutput),
                ),
                Decimal::add(
                    Decimal::mul((string) $usage->cacheRead, (string) $this->rateCacheRead),
                    Decimal::mul((string) $usage->cacheWrite, (string) $this->rateCacheWrite),
                ),
            );
        } else {
            $sum = Decimal::mul((string) $usage->total(), (string) $this->rateAny);
        }

        $cost = Decimal::div($sum, '1000000');

        $offPeak = $this->model->provider()->offPeak();
        if ($offPeak !== null && $offPeak->contains($ts)) {
            $cost = Decimal::mul($cost, $offPeak->multiplierValue());
        }

        return Decimal::roundEvent($cost);
    }
}
